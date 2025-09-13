<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\TarifLivraison;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TarifLivraisonRequest;

class TarifLivraisonController extends Controller
{
    public function index()
    {
        try {
            $tarifLivraisons = TarifLivraison::all();

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
            $tarifLivraison = TarifLivraison::create($input);

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
            $input = $request->validated();
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
            $tarifLivraisons = TarifLivraison::onlyTrashed()->get();

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
