<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationCommande;
use App\Http\Requests\NotificationCommandeRequest;

class NotificationCommandeController extends Controller
{
    public function index()
    {
        try {
            $notificationCommandes = NotificationCommande::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $notificationCommandes->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $notificationCommandes
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

    public function store(NotificationCommandeRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $notificationCommande = NotificationCommande::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $notificationCommande
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

    public function show(NotificationCommande $notificationCommande)
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Données récupérées avec succès',
                'data' => $notificationCommande
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
