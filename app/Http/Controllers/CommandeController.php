<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CommandeRequest;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Commande::with([
            'client:id,name,email,phone',
            'restaurateur:id,name,email,phone',
            'moyenPaiement:id,nom',
            'quartierLivraison:id,nom'
        ]);

        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('restaurateur_id')) {
            $query->where('restaurateur_id', $request->restaurateur_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_commande', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('restaurateur', function ($restQuery) use ($search) {
                        $restQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $commandes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $commandes->items(),
            'meta' => [
                'current_page' => $commandes->currentPage(),
                'per_page' => $commandes->perPage(),
                'total' => $commandes->total(),
                'last_page' => $commandes->lastPage(),
            ]
        ]);
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
            $commande = Commande::with([
                'client:id,name,email,phone,address',
                'restaurateur:id,name,email,phone',
                'moyenPaiement:id,nom,icon',
                'quartierLivraison:id,nom',
                'items.plat:id,nom,description,prix,image'
            ])->findOrFail($commande->id);

            return response()->json([
                'success' => true,
                'data' => $commande
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Une erreur s\'est produite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $commande = Commande::findOrFail($id);

            $request->validate([
                'status' => 'sometimes|in:en_attente,confirmee,prete,en_livraison,recuperee',
                'status_paiement' => 'sometimes|boolean',
                'temps_preparation_estime' => 'sometimes|integer|min:1'
            ]);

            $commande->update($request->only([
                'status',
                'status_paiement',
                'temps_preparation_estime'
            ]));

            return response()->json([
                'success' => true,
                'data' => $commande->load([
                    'client:id,name',
                    'restaurateur:id,name',
                    'moyenPaiement:id,nom'
                ]),
                'message' => 'Commande mise à jour avec succès'
            ]);
            $commande = Commande::findOrFail($id);

            $request->validate([
                'status' => 'sometimes|in:en_attente,confirmee,prete,en_livraison,recuperee',
                'status_paiement' => 'sometimes|boolean',
                'temps_preparation_estime' => 'sometimes|integer|min:1'
            ]);

            $commande->update($request->only([
                'status',
                'status_paiement',
                'temps_preparation_estime'
            ]));

            return response()->json([
                'success' => true,
                'data' => $commande->load([
                    'client:id,name',
                    'restaurateur:id,name',
                    'moyenPaiement:id,nom'
                ]),
                'message' => 'Commande mise à jour avec succès'
            ]);
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
