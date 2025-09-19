<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\MoyenPaiement;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MoyenPaiementRequest;

class MoyenPaiementController extends Controller
{
    public function index()
    {
        try {
            $moyenPaiements = MoyenPaiement::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $moyenPaiements->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $moyenPaiements
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

    public function store(MoyenPaiementRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $input['created_by'] = auth()->user()->id ?? null;
            $moyenPaiement = MoyenPaiement::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $moyenPaiement
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

    public function show(MoyenPaiement $moyenPaiement)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $moyenPaiement
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

    public function update(MoyenPaiementRequest $request, MoyenPaiement $moyenPaiement)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $moyenPaiement->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $moyenPaiement
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

    public function destroy(MoyenPaiement $moyenPaiement)
    {
        try {
            $moyenPaiement->delete();

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
            $moyenPaiements = MoyenPaiement::onlyTrashed()->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $moyenPaiements->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $moyenPaiements
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

    public function restore(MoyenPaiement $moyenPaiement)
    {
        try {
            $moyenPaiement->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $moyenPaiement
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
