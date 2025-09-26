<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\RestaurateurMoyenPaiement;
use App\Http\Requests\RestaurateurMoyenPaiementRequest;

class RestaurateurMoyenPaiementController extends Controller
{
    /**
     * Récupère les moyens de paiement d'un restaurateur avec les détails complets
     */
    public function index(Request $request)
    {
        try {
            $query = RestaurateurMoyenPaiement::with(['moyenPaiement', 'restaurateur']);

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

            $restaurateurMoyenPaiements = $query->get();

            // Transformer les données pour inclure les informations du moyen de paiement
            $formattedData = $restaurateurMoyenPaiements->map(function ($rmp) {
                return [
                    'id' => $rmp->id,
                    'restaurateur_id' => $rmp->restaurateur_id,
                    'moyen_paiement_id' => $rmp->moyen_paiement_id,
                    'numero_compte' => $rmp->numero_compte,
                    'nom_titulaire' => $rmp->nom_titulaire,
                    'created_at' => $rmp->created_at,
                    'updated_at' => $rmp->updated_at,
                    // Informations du moyen de paiement
                    'moyen_paiement' => [
                        'id' => $rmp->moyenPaiement->id,
                        'nom' => $rmp->moyenPaiement->nom,
                        'icon' => $rmp->moyenPaiement->icon,
                        'code' => $rmp->moyenPaiement->code ?? null,
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $formattedData->isEmpty() ? 'Aucun moyen de paiement configuré' : 'Moyens de paiement récupérés avec succès',
                'data' => $formattedData
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

    public function store(RestaurateurMoyenPaiementRequest $request)
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

            // Vérifier si ce moyen de paiement n'existe pas déjà pour ce restaurateur
            $existing = RestaurateurMoyenPaiement::where('restaurateur_id', $input['restaurateur_id'])
                ->where('moyen_paiement_id', $input['moyen_paiement_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Ce moyen de paiement est déjà configuré pour ce restaurateur'
                ], 409);
            }

            $restaurateurMoyenPaiement = RestaurateurMoyenPaiement::create($input);

            // Charger les relations
            $restaurateurMoyenPaiement->load(['moyenPaiement', 'restaurateur']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Moyen de paiement ajouté avec succès',
                'data' => [
                    'id' => $restaurateurMoyenPaiement->id,
                    'restaurateur_id' => $restaurateurMoyenPaiement->restaurateur_id,
                    'moyen_paiement_id' => $restaurateurMoyenPaiement->moyen_paiement_id,
                    'numero_compte' => $restaurateurMoyenPaiement->numero_compte,
                    'nom_titulaire' => $restaurateurMoyenPaiement->nom_titulaire,
                    'created_at' => $restaurateurMoyenPaiement->created_at,
                    'updated_at' => $restaurateurMoyenPaiement->updated_at,
                    'moyen_paiement' => [
                        'id' => $restaurateurMoyenPaiement->moyenPaiement->id,
                        'nom' => $restaurateurMoyenPaiement->moyenPaiement->nom,
                        'icon' => $restaurateurMoyenPaiement->moyenPaiement->icon,
                    ]
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite lors de l\'ajout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        try {
            // Vérifier que l'utilisateur peut accéder à cette ressource
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $restaurateurMoyenPaiement->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $restaurateurMoyenPaiement->load(['moyenPaiement', 'restaurateur']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => [
                    'id' => $restaurateurMoyenPaiement->id,
                    'restaurateur_id' => $restaurateurMoyenPaiement->restaurateur_id,
                    'moyen_paiement_id' => $restaurateurMoyenPaiement->moyen_paiement_id,
                    'numero_compte' => $restaurateurMoyenPaiement->numero_compte,
                    'nom_titulaire' => $restaurateurMoyenPaiement->nom_titulaire,
                    'created_at' => $restaurateurMoyenPaiement->created_at,
                    'updated_at' => $restaurateurMoyenPaiement->updated_at,
                    'moyen_paiement' => [
                        'id' => $restaurateurMoyenPaiement->moyenPaiement->id,
                        'nom' => $restaurateurMoyenPaiement->moyenPaiement->nom,
                        'icon' => $restaurateurMoyenPaiement->moyenPaiement->icon,
                    ],
                    'restaurateur' => [
                        'id' => $restaurateurMoyenPaiement->restaurateur->id,
                        'name' => $restaurateurMoyenPaiement->restaurateur->name,
                        'email' => $restaurateurMoyenPaiement->restaurateur->email,
                    ]
                ]
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

    public function update(RestaurateurMoyenPaiementRequest $request, RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        DB::beginTransaction();

        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $restaurateurMoyenPaiement->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $input = $request->validated();

            // ✅ Ne pas permettre de changer le restaurateur_id ou moyen_paiement_id lors d'une mise à jour
            unset($input['restaurateur_id'], $input['moyen_paiement_id']);

            $restaurateurMoyenPaiement->update($input);

            // Recharger les relations
            $restaurateurMoyenPaiement->load(['moyenPaiement']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Moyen de paiement mis à jour avec succès',
                'data' => [
                    'id' => $restaurateurMoyenPaiement->id,
                    'restaurateur_id' => $restaurateurMoyenPaiement->restaurateur_id,
                    'moyen_paiement_id' => $restaurateurMoyenPaiement->moyen_paiement_id,
                    'numero_compte' => $restaurateurMoyenPaiement->numero_compte,
                    'nom_titulaire' => $restaurateurMoyenPaiement->nom_titulaire,
                    'created_at' => $restaurateurMoyenPaiement->created_at,
                    'updated_at' => $restaurateurMoyenPaiement->updated_at,
                    'moyen_paiement' => [
                        'id' => $restaurateurMoyenPaiement->moyenPaiement->id,
                        'nom' => $restaurateurMoyenPaiement->moyenPaiement->nom,
                        'icon' => $restaurateurMoyenPaiement->moyenPaiement->icon,
                    ]
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $restaurateurMoyenPaiement->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $restaurateurMoyenPaiement->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Moyen de paiement supprimé avec succès',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $query = RestaurateurMoyenPaiement::onlyTrashed()->with(['moyenPaiement', 'restaurateur']);

            // Filtrer par restaurateur connecté
            $user = Auth::user();
            if ($user->role !== 'admin') {
                $query->where('restaurateur_id', $user->id);
            }

            $restaurateurMoyenPaiements = $query->get();

            $formattedData = $restaurateurMoyenPaiements->map(function ($rmp) {
                return [
                    'id' => $rmp->id,
                    'restaurateur_id' => $rmp->restaurateur_id,
                    'moyen_paiement_id' => $rmp->moyen_paiement_id,
                    'numero_compte' => $rmp->numero_compte,
                    'nom_titulaire' => $rmp->nom_titulaire,
                    'deleted_at' => $rmp->deleted_at,
                    'moyen_paiement' => [
                        'id' => $rmp->moyenPaiement->id,
                        'nom' => $rmp->moyenPaiement->nom,
                        'icon' => $rmp->moyenPaiement->icon,
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $formattedData->isEmpty() ? 'Aucun enregistrement supprimé trouvé' : 'Enregistrements supprimés récupérés avec succès',
                'data' => $formattedData
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

    public function restore(RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->id !== $restaurateurMoyenPaiement->restaurateur_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $restaurateurMoyenPaiement->restore();
            $restaurateurMoyenPaiement->load(['moyenPaiement']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'code' => 200,
                'message' => 'Moyen de paiement restauré avec succès',
                'data' => [
                    'id' => $restaurateurMoyenPaiement->id,
                    'restaurateur_id' => $restaurateurMoyenPaiement->restaurateur_id,
                    'moyen_paiement_id' => $restaurateurMoyenPaiement->moyen_paiement_id,
                    'numero_compte' => $restaurateurMoyenPaiement->numero_compte,
                    'nom_titulaire' => $restaurateurMoyenPaiement->nom_titulaire,
                    'moyen_paiement' => [
                        'id' => $restaurateurMoyenPaiement->moyenPaiement->id,
                        'nom' => $restaurateurMoyenPaiement->moyenPaiement->nom,
                        'icon' => $restaurateurMoyenPaiement->moyenPaiement->icon,
                    ]
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite lors de la restauration',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
