<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Quartier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\QuartierRequest;

class QuartierController extends Controller
{
    public function index()
    {
        try {
            $quartiers = Quartier::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $quartiers->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $quartiers
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

    public function store(QuartierRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $input['created_by'] = auth()->user()->id ?? null;
            $quartier = Quartier::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $quartier
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

    public function show(Quartier $quartier)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $quartier
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

    public function update(QuartierRequest $request, Quartier $quartier)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $quartier->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $quartier
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

    public function destroy(Quartier $quartier)
    {
        try {
            $quartier->delete();

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
            $quartiers = Quartier::onlyTrashed()->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $quartiers->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $quartiers
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

    public function restore(Quartier $quartier)
    {
        try {
            $quartier->restore();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement restauré avec succès',
                'data' => $quartier
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
