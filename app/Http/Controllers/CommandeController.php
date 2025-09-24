<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Commande::with([
                'client:id,name,phone,email',
                'restaurateur:id,name',
                'moyenPaiement:id,nom,icon',
                'quartierLivraison:id,nom',
                'items.plat:id,nom,description,prix,image'
            ]);

            // Filtres
            if ($request->has('status') && $request->status !== 'all') {
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
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('numero_commande', 'like', "%{$search}%")
                      ->orWhereHas('client', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('restaurateur', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
                });
            }

            $perPage = (int) $request->get('per_page', 12);
            $commandes = $query->orderBy('created_at', 'desc')->paginate($perPage);

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

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des commandes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des commandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $commande = Commande::with([
                'client:id,name,phone,email,address',
                'restaurateur:id,name,phone,email',
                'moyenPaiement:id,nom,icon',
                'quartierLivraison:id,nom',
                'items.plat:id,nom,description,prix,image,temps_preparation'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $commande
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la commande: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $commande = Commande::with([
    //             'client:id,name,phone,address',
    //             'restaurateur:id,name,phone',
    //             'moyenPaiement:id,nom,icon',
    //             'quartierLivraison:id,nom',
    //             'items.plat:id,nom,description,prix,image'
    //         ])->findOrFail($id);

    //         return response()->json(['success' => true, 'data' => $commande]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Commande non trouvée', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        try {
            $commande = Commande::findOrFail($id);

            $request->validate([
                // Valide selon la nouvelle nomenclature
                'status' => 'sometimes|in:en_attente,confirmee,prete,en_livraison,recuperee,annulee',
                'status_paiement' => 'sometimes|boolean',
                'temps_preparation_estime' => 'sometimes|integer|min:1'
            ]);

            $updateData = $request->only(['status', 'status_paiement', 'temps_preparation_estime']);
            $commande->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $commande->fresh()->load(['client:id,name', 'restaurateur:id,name', 'moyenPaiement:id,nom']),
                'message' => 'Commande mise à jour'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur mise à jour', 'error' => $e->getMessage()], 500);
        }
    }
}
