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
}
