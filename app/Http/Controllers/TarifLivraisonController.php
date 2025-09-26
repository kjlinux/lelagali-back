<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\TarifLivraison;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TarifLivraisonRequest;

class TarifLivraisonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TarifLivraison::query();
            
            // Filtrer par restaurateur si spécifié
            if ($request->has('restaurateur_id')) {
                $query->where('restaurateur_id', $request->restaurateur_id);
            } else {
                // Par défaut, filtrer par l'utilisateur connecté s'il est restaurateur
                $user = Auth::user();
                if ($user && $user->role === 'restaurateur') {
                    $query->where('restaurateur_id', $user->id);
                }
            }

            $tarifLivraisons = $query->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $tarifLivraisons->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $tarifLivraisons
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

    public function store(TarifLivraisonRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            
            // ✅ Fix: Assigner l'utilisateur connecté si pas de restaurateur_id spécifié
            if (!isset($input['restaurateur_id'])) {
                $user = Auth::user();
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 401,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }
                $input['restaurateur_id'] = $user->id;
            }
            
            // Vérifier si un tarif existe déjà pour ce restaurateur et ce quartier
            $existing = TarifLivraison::where('restaurateur_id', $input['restaurateur_id'])
                ->where('quartier_id', $input['quartier_id'])
                ->first();

            if ($existing) {
                // Mettre à jour le tarif existant au lieu de créer un nouveau
                $existing->update(['prix' => $input['prix']]);
                $tarifLivraison = $existing;
            } else {
                // Créer un nouveau tarif
                $tarifLivraison = TarifLivraison::create($input);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $tarifLivraison
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

    public function show(TarifLivraison $tarifLivraison)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $tarifLivraison->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $tarifLivraison
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

    public function update(TarifLivraisonRequest $request, TarifLivraison $tarifLivraison)
    {
        DB::beginTransaction();

        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $tarifLivraison->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $input = $request->validated();
            
            // ✅ Ne pas permettre de changer le restaurateur_id lors d'une mise à jour
            unset($input['restaurateur_id']);
            
            $tarifLivraison->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $tarifLivraison
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

    public function destroy(TarifLivraison $tarifLivraison)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $tarifLivraison->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $tarifLivraison->delete();

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
            $query = TarifLivraison::onlyTrashed();
            
            // Filtrer par restaurateur connecté
            $user = Auth::user();
            if ($user->role !== 'admin') {
                $query->where('restaurateur_id', $user->id);
            }
            
            $tarifLivraisons = $query->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $tarifLivraisons->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $tarifLivraisons
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

    public function restore(TarifLivraison $tarifLivraison)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $tarifLivraison->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $tarifLivraison->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $tarifLivraison
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
}