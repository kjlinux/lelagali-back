<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CommandeRequest;

class CommandeController extends Controller
{
    public function index()
    {
        try {
            $commandes = Commande::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $commandes->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $commandes
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

    public function store(CommandeRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $commande = Commande::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $commande
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

    public function show(Commande $commande)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $commande
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
