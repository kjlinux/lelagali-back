<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\CommandeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CommandeItemRequest;

class CommandeItemController extends Controller
{
    public function index()
    {
        try {
            $commandeItems = CommandeItem::all();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $commandeItems->isEmpty() ? 'Aucun enregistrement trouvé' : 'Données récupérées avec succès',
                'data' => $commandeItems
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

    public function store(CommandeItemRequest $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $commandeItem = CommandeItem::create($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Enregistrement créé avec succès',
                'data' => $commandeItem
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

    public function update(CommandeItemRequest $request, CommandeItem $commandeItem)
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $commandeItem->update($input);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Enregistrement mis à jour avec succès',
                'data' => $commandeItem
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
}
