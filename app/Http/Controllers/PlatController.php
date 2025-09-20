<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Plat;
use Illuminate\Http\Request;
use App\Http\Requests\PlatRequest;
use Illuminate\Support\Facades\DB;

class PlatController extends Controller
{
    public function index()
    {
        try {
            $plats = Plat::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $plats->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $plats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(PlatRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $plat = Plat::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $plat
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Plat $plat)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $plat
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(PlatRequest $request, Plat $plat)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $plat->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $plat
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Plat $plat)
    {
        try {
            $plat->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement supprimé avec succès',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $plats = Plat::onlyTrashed()->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $plats->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $plats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore(Plat $plat)
    {
        try {
            $plat->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $plat
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des plats pour la modération
     */
    public function moderation(Request $request)
    {
        try {
            $query = Plat::with(['restaurateur:id,name'])
                ->select([
                    'id',
                    'nom',
                    'description',
                    'prix',
                    'quantite_disponible',
                    'date_disponibilite',
                    'is_approved',
                    'approved_by',
                    'approved_at',
                    'restaurateur_id',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc');

            // Filtrer par statut d'approbation si demandé
            if ($request->has('status')) {
                $status = $request->get('status');
                if ($status === 'pending') {
                    $query->where('is_approved', false);
                } elseif ($status === 'approved') {
                    $query->where('is_approved', true);
                }
            }

            // Recherche par nom, description ou restaurateur
            if ($request->has('search') && !empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('restaurateur', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $plats = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'status' => 'success',
                'message' => 'Plats récupérés avec succès',
                'data' => $plats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des plats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approuver un plat
     */
    public function approve(Request $request, Plat $plat)
    {
        try {
            $user = $request->user();

            // Vérifier si l'utilisateur a le rôle admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas les permissions nécessaires'
                ], 403);
            }

            // Si le plat est déjà approuvé
            if ($plat->is_approved) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Ce plat est déjà approuvé'
                ], 200);
            }

            $plat->update([
                'is_approved' => true,
                'approved_by' => $user->id,
                'approved_at' => now()
            ]);

            $plat->load('restaurateur:id,name');

            return response()->json([
                'status' => 'success',
                'message' => 'Plat approuvé avec succès',
                'data' => $plat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'approbation du plat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter un plat (le désapprouver)
     */
    public function reject(Request $request, Plat $plat)
    {
        try {
            $user = $request->user();

            // Vérifier si l'utilisateur a le rôle admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas les permissions nécessaires'
                ], 403);
            }

            // Si le plat n'est pas approuvé
            if (!$plat->is_approved) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Ce plat n\'est pas approuvé'
                ], 200);
            }

            $plat->update([
                'is_approved' => false,
                'approved_by' => $user->id,
                'approved_at' => now()
            ]);

            $plat->load('restaurateur:id,name');

            return response()->json([
                'status' => 'success',
                'message' => 'Plat rejeté avec succès',
                'data' => $plat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du rejet du plat',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
