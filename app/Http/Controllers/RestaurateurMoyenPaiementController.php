<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RestaurateurMoyenPaiement;
use App\Http\Requests\RestaurateurMoyenPaiementRequest;

class RestaurateurMoyenPaiementController extends Controller
{
    public function index()
    {
        try {
            $restaurateurMoyenPaiements = RestaurateurMoyenPaiement::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $restaurateurMoyenPaiements->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $restaurateurMoyenPaiements
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
            $restaurateurMoyenPaiement = RestaurateurMoyenPaiement::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $restaurateurMoyenPaiement
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

    public function show(RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $restaurateurMoyenPaiement
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
            $input = $request->validated();
            $restaurateurMoyenPaiement->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $restaurateurMoyenPaiement
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

    public function destroy(RestaurateurMoyenPaiement $restaurateurMoyenPaiement)
    {
        try {
            $restaurateurMoyenPaiement->delete();

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
            $restaurateurMoyenPaiements = RestaurateurMoyenPaiement::onlyTrashed()->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $restaurateurMoyenPaiements->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $restaurateurMoyenPaiements
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
            $restaurateurMoyenPaiement->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $restaurateurMoyenPaiement
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
