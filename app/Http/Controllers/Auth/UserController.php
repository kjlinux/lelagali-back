<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Mail\UserRegistrationMail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\UserPasswordResetMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsUpdateMail;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['quartier']);

        // Filtrer par rôle si spécifié
        if ($request->has('role')) {
            $query->where('role', $request->role);

            // Si le rôle est restaurateur, on charge aussi le nombre de commandes
            if ($request->role === 'restaurateur' || $request->role === 'client') {
                $query->withCount('commandes');
            }
        }

        $users = $query->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => $users->isEmpty() ? 'Aucun utilisateur trouvé' : 'Liste des utilisateurs récupérée avec succès',
            'data' => $users
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string',
                'email' => 'nullable|email|unique:users',
                'phone' => 'required|string|unique:users',
                'role' => 'sometimes|in:client,restaurateur,admin',
                'address' => 'nullable|string',
                'quartier_id' => 'nullable|exists:quartiers,id',
                'profile_image' => 'nullable|string',
            ]);

            // Générer un mot de passe de 12 caractères si non fourni
            $password = $request->has('password') && !empty($request->password)
                ? $request->password
                : $this->generateSecurePassword(12);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($password),
                'role' => $request->role ?? 'client',
                'address' => $request->address,
                'quartier_id' => $request->quartier_id,
                'profile_image' => $request->profile_image,
                'active' => true,
            ]);

            // Charger les relations
            $user->load(['quartier']);

            DB::commit();

            // Envoyer l'email si une adresse email est fournie
            if ($user->email) {
                Mail::to($user->email)->send(new UserRegistrationMail($user, $password));
            }

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Utilisateur créé avec succès',
                'data' => $user,
                'token' => JWTAuth::fromUser($user)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur est survenue lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'sometimes|string',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
                'role' => 'sometimes|in:client,restaurateur,admin',
                'address' => 'nullable|string',
                'quartier_id' => 'nullable|exists:quartiers,id',
                'profile_image' => 'nullable|string',
                'active' => 'sometimes|boolean',
            ]);

            if ($request->has('name')) $user->name = $request->name;
            if ($request->has('email')) $user->email = $request->email;
            if ($request->has('phone')) $user->phone = $request->phone;
            if ($request->has('role')) $user->role = $request->role;
            if ($request->has('address')) $user->address = $request->address;
            if ($request->has('quartier_id')) $user->quartier_id = $request->quartier_id;
            if ($request->has('profile_image')) $user->profile_image = $request->profile_image;
            if ($request->has('active')) $user->active = $request->active;

            $passToEmail = null;
            if ($request->has('password') && !empty($request->password)) {
                $user->password = Hash::make($request->password);
                $passToEmail = $request->password;
            }

            $user->save();

            // Charger les relations
            $user->load(['quartier']);

            DB::commit();

            // Envoyer l'email si le mot de passe a été modifié
            if ($passToEmail && $user->email) {
                Mail::to($user->email)->send(new UserCredentialsUpdateMail($user, $passToEmail));
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => "Erreur système",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();

            $rules = [
                'username' => 'required|unique:users,username,' . $user->id,
                'email'    => 'required|email|unique:users,email,' . $user->id,
                'name'     => 'required',
                'surname'  => 'required',
            ];

            if ($request->filled('current_password') || $request->filled('new_password') || $request->filled('confirm_new_password')) {
                $rules['current_password']    = 'required';
                $rules['new_password']        = 'required|min:6';
                $rules['confirm_new_password'] = 'required|same:new_password';
            }

            $data = $request->validate($rules);

            $user->username = $data['username'];
            $user->email    = $data['email'];
            $user->name     = $data['name'];
            $user->surname  = $data['surname'];

            $passToEmail = null;
            if (!empty($data['current_password'])) {
                if (!Hash::check($data['current_password'], $user->password)) {
                    return response()->json([
                        'status'  => 'error',
                        'code'    => 400,
                        'message' => 'Le mot de passe actuel est incorrect'
                    ], 400);
                }
                $user->password = Hash::make($data['new_password']);
                $passToEmail = $data['new_password'];
            }

            $user->save();
            DB::commit();

            if ($passToEmail) {
                Mail::to($user->email)->send(new UserCredentialsUpdateMail($user, $passToEmail));
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Profil mis à jour avec succès',
                'data'    => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => "Erreur système",
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();

            $newPassword = $request->has('password') && !empty($request->password)
                ? $request->password
                : $this->generateSecurePassword(12);

            $user->password = Hash::make($newPassword);
            $user->save();

            DB::commit();

            if ($user->email) {
                Mail::to($user->email)->send(new UserPasswordResetMail($user, $newPassword));
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Mot de passe réinitialisé avec succès',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => "Erreur système",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $login = $request->input('email');
        $password = $request->input('password');

        $credentialsEmail = ['email' => $login, 'password' => $password];
        if ($token = Auth::attempt($credentialsEmail)) {
            return $this->respondWithToken($token);
        }

        $credentialsUsername = ['username' => $login, 'password' => $password];
        if ($token = Auth::attempt($credentialsUsername)) {
            return $this->respondWithToken($token);
        }

        return response()->json([
            'status' => 'error',
            'code' => 401,
            'message' => 'Identifiants invalides'
        ], 401);
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => Auth::user()->load(['quartier'])
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Détails de l\'utilisateur récupérés avec succès',
            'data' => $user->load(['quartier'])
        ]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'role' => 'required|string'
            ]);

            $user->syncRoles([$validated['role']]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Rôle de l\'utilisateur mis à jour avec succès',
                'data'    => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => "Erreur système",
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => "Erreur système",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Utilisateur restauré avec succès',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => "Erreur système",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed(): JsonResponse
    {
        $users = User::onlyTrashed()->get();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => $users->isEmpty() ? 'Aucun utilisateur supprimé trouvé' : 'Liste des utilisateurs supprimés récupérée avec succès',
            'data' => $users
        ]);
    }

    /**
     * Génère un mot de passe sécurisé
     *
     * @param int $length
     * @return string
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        $password = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'profile' => Auth::user()->load(['quartier'])
        ]);
    }
}
