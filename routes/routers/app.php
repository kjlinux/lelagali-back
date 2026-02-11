<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CommandeItemController;
use App\Http\Controllers\TarifLivraisonController;
use App\Http\Controllers\NotificationCommandeController;
use App\Http\Controllers\RestaurateurMoyenPaiementController;

// Routes des commandes
Route::get('commandes', [CommandeController::class, 'index']);
Route::post('commandes', [CommandeController::class, 'store']);
Route::get('commandes/{commande}', [CommandeController::class, 'show']);
Route::get('/commandes', [CommandeController::class, 'index']);
Route::get('/commandes/{id}', [CommandeController::class, 'show']);
Route::put('/commandes/{id}', [CommandeController::class, 'update']);

// Routes des items de commande
Route::get('commande-items', [CommandeItemController::class, 'index']);
Route::post('commande-items', [CommandeItemController::class, 'store']);
Route::put('commande-items/{commandeItem}', [CommandeItemController::class, 'update']);

// Routes des commandes
Route::prefix('app/commandes')->group(function () {
    // Liste des commandes avec filtres et pagination
    Route::get('/', [CommandeController::class, 'index']);

    // Créer une nouvelle commande
    Route::post('/', [CommandeController::class, 'store']);

    // Détails d'une commande spécifique
    Route::get('/{id}', [CommandeController::class, 'show']);

    // Mettre à jour une commande (statut, paiement, etc.)
    Route::put('/{id}', [CommandeController::class, 'update']);

    // Supprimer une commande (soft delete)
    Route::delete('/{id}', [CommandeController::class, 'destroy']);

    // Statistiques des commandes
    Route::get('/stats/dashboard', [CommandeController::class, 'statistics']);
});

// Routes spécifiques pour les actions sur les commandes
Route::prefix('commandes/{id}')->group(function () {
    // Confirmer le paiement d'une commande
    Route::patch('/confirmer-paiement', [CommandeController::class, 'confirmerPaiement']);

    // Rejeter le paiement d'une commande
    Route::post('/rejeter-paiement', [CommandeController::class, 'rejeterPaiement']);

    // Accepter une commande
    Route::patch('/accept', function ($id) {
        $commande = \App\Models\Commande::findOrFail($id);
        $oldStatus = $commande->status;

        if ($commande->accepter()) {
            $commande->load(['client:id,name,email', 'restaurateur:id,name', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

            try {
                if ($commande->client->email) {
                    \Illuminate\Support\Facades\Mail::to($commande->client->email)
                        ->send(new \App\Mail\OrderStatusChangedMail($commande, $oldStatus, 'confirmee'));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Erreur envoi email statut confirmee pour ' . $commande->numero_commande . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Commande acceptée',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'accepter cette commande'
        ], 400);
    });

    // Marquer comme prête
    Route::patch('/ready', function ($id) {
        $commande = \App\Models\Commande::findOrFail($id);
        $oldStatus = $commande->status;

        if ($commande->marquerPrete()) {
            $commande->load(['client:id,name,email', 'restaurateur:id,name', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

            try {
                if ($commande->client->email) {
                    \Illuminate\Support\Facades\Mail::to($commande->client->email)
                        ->send(new \App\Mail\OrderStatusChangedMail($commande, $oldStatus, 'prete'));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Erreur envoi email statut prete pour ' . $commande->numero_commande . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Commande marquée comme prête',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible de marquer cette commande comme prête'
        ], 400);
    });

    // Mettre en livraison
    Route::patch('/deliver', function ($id) {
        $commande = \App\Models\Commande::findOrFail($id);
        $oldStatus = $commande->status;

        if ($commande->mettreEnLivraison()) {
            $commande->load(['client:id,name,email', 'restaurateur:id,name', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

            try {
                if ($commande->client->email) {
                    \Illuminate\Support\Facades\Mail::to($commande->client->email)
                        ->send(new \App\Mail\OrderStatusChangedMail($commande, $oldStatus, 'en_livraison'));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Erreur envoi email statut en_livraison pour ' . $commande->numero_commande . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Commande en cours de livraison',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible de mettre cette commande en livraison'
        ], 400);
    });

    // Marquer comme récupérée/livrée
    Route::patch('/complete', function ($id) {
        $commande = \App\Models\Commande::findOrFail($id);
        $oldStatus = $commande->status;

        if ($commande->marquerRecuperee()) {
            $commande->load(['client:id,name,email', 'restaurateur:id,name', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

            try {
                if ($commande->client->email) {
                    \Illuminate\Support\Facades\Mail::to($commande->client->email)
                        ->send(new \App\Mail\OrderStatusChangedMail($commande, $oldStatus, 'recuperee'));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Erreur envoi email statut recuperee pour ' . $commande->numero_commande . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Commande terminée',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible de terminer cette commande'
        ], 400);
    });

    // Annuler une commande
    Route::patch('/cancel', function (Request $request, $id) {
        $request->validate([
            'raison' => 'sometimes|string|max:500',
            'cancelled_by' => 'sometimes|in:client,restaurant' // Identifier qui annule
        ]);

        $commande = \App\Models\Commande::findOrFail($id);
        $raison = $request->input('raison');
        $cancelledBy = $request->input('cancelled_by', 'client');

        if ($commande->annuler($raison)) {
            $commande->load(['client:id,name,email', 'restaurateur:id,name,email', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

            // Si annulée par le client, envoyer email au restaurant
            if ($cancelledBy === 'client' && $commande->restaurateur->email) {
                \Illuminate\Support\Facades\Mail::to($commande->restaurateur->email)
                    ->send(new \App\Mail\OrderCancelledByClientMail($commande));
            }

            return response()->json([
                'success' => true,
                'message' => 'Commande annulée',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'annuler cette commande'
        ], 400);
    });

    // Marquer comme payée
    Route::patch('/mark-paid', function (Request $request, $id) {
        $request->validate([
            'reference_paiement' => 'sometimes|string|max:100',
            'numero_paiement' => 'sometimes|string|max:20'
        ]);

        $commande = \App\Models\Commande::findOrFail($id);
        $reference = $request->input('reference_paiement');
        $numero = $request->input('numero_paiement');

        if ($commande->marquerPayee($reference, $numero)) {
            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé',
                'data' => $commande->fresh()
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Impossible de confirmer le paiement'
        ], 400);
    });

    // Rejeter le paiement
    Route::patch('/reject-payment', function (Request $request, $id) {
        $request->validate([
            'raison' => 'sometimes|string|max:500'
        ]);

        $commande = \App\Models\Commande::findOrFail($id);
        $raison = $request->input('raison');

        // Marquer le paiement comme non confirmé
        $commande->status_paiement = false;
        $commande->save();

        $commande->load(['client:id,name,email', 'restaurateur:id,name', 'moyenPaiement:id,nom', 'quartierLivraison:id,nom', 'items.plat:id,nom,prix']);

        // Envoyer l'email au client
        if ($commande->client->email) {
            \Illuminate\Support\Facades\Mail::to($commande->client->email)
                ->send(new \App\Mail\PaymentRejectedMail($commande, $raison));
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement rejeté',
            'data' => $commande->fresh()
        ]);
    });
});

// Routes pour les items de commande
Route::prefix('app/commande-items')->group(function () {
    // Liste des items d'une commande
    Route::get('/', function (Request $request) {
        $query = \App\Models\CommandeItem::with(['plat:id,nom,description,prix,image', 'commande:id,numero_commande']);

        if ($request->has('commande_id')) {
            $query->where('commande_id', $request->commande_id);
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    });

    // Ajouter un item à une commande
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'commande_id' => 'required|uuid|exists:commandes,id',
            'plat_id' => 'required|uuid|exists:plats,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|integer|min:0',
            'notes' => 'sometimes|string|max:500'
        ]);

        $item = \App\Models\CommandeItem::create($validated);

        // Recalculer les totaux de la commande
        $item->commande->recalculerTotaux();

        return response()->json([
            'success' => true,
            'data' => $item->load(['plat:id,nom,prix', 'commande:id,numero_commande']),
            'message' => 'Item ajouté à la commande'
        ], 201);
    });

    // Mettre à jour un item de commande
    Route::put('/{itemId}', function (Request $request, $itemId) {
        $item = \App\Models\CommandeItem::findOrFail($itemId);

        $validated = $request->validate([
            'quantite' => 'sometimes|integer|min:1',
            'prix_unitaire' => 'sometimes|integer|min:0',
            'notes' => 'sometimes|string|max:500'
        ]);

        $item->update($validated);

        // Recalculer les totaux de la commande
        $item->commande->recalculerTotaux();

        return response()->json([
            'success' => true,
            'data' => $item->fresh(['plat:id,nom,prix', 'commande:id,numero_commande']),
            'message' => 'Item mis à jour'
        ]);
    });

    // Supprimer un item de commande
    Route::delete('/{itemId}', function ($itemId) {
        $item = \App\Models\CommandeItem::findOrFail($itemId);
        $commande = $item->commande;

        $item->delete();

        // Recalculer les totaux de la commande
        $commande->recalculerTotaux();

        return response()->json([
            'success' => true,
            'message' => 'Item supprimé de la commande'
        ]);
    });
});

// Routes pour les moyens de paiement
Route::prefix('app/moyens-paiement')->group(function () {
    Route::get('/', function () {
        $moyens = \App\Models\MoyenPaiement::all();
        return response()->json([
            'success' => true,
            'data' => $moyens
        ]);
    });
});

// Routes pour les quartiers
Route::prefix('app/quartiers')->group(function () {
    Route::get('/', function () {
        $quartiers = \App\Models\Quartier::all();
        return response()->json([
            'success' => true,
            'data' => $quartiers
        ]);
    });
});

// Routes de recherche et de filtrage avancé
Route::prefix('app/search')->group(function () {
    // Recherche de commandes avec filtres avancés
    Route::post('/commandes', function (Request $request) {
        $query = \App\Models\Commande::with([
            'client:id,name,phone',
            'restaurateur:id,name',
            'moyenPaiement:id,nom',
            'quartierLivraison:id,nom',
            'items.plat:id,nom,prix'
        ]);

        // Filtres avancés
        if ($request->filled('status')) {
            if (is_array($request->status)) {
                $query->whereIn('status', $request->status);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('type_service')) {
            $query->where('type_service', $request->type_service);
        }

        if ($request->filled('status_paiement')) {
            $query->where('status_paiement', $request->boolean('status_paiement'));
        }

        if ($request->filled('montant_min')) {
            $query->where('total_general', '>=', $request->montant_min);
        }

        if ($request->filled('montant_max')) {
            $query->where('total_general', '<=', $request->montant_max);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        if ($request->filled('restaurateur_ids')) {
            $query->whereIn('restaurateur_id', $request->restaurateur_ids);
        }

        if ($request->filled('client_ids')) {
            $query->whereIn('client_id', $request->client_ids);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = min($request->get('per_page', 15), 50); // Max 50 par page
        $commandes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $commandes->items(),
            'meta' => [
                'current_page' => $commandes->currentPage(),
                'per_page' => $commandes->perPage(),
                'total' => $commandes->total(),
                'last_page' => $commandes->lastPage(),
                'from' => $commandes->firstItem(),
                'to' => $commandes->lastItem(),
            ]
        ]);
    });
});

// Routes de rapport et d'export
Route::prefix('app/reports')->group(function () {
    // Rapport des ventes par période
    Route::get('/sales', function (Request $request) {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'restaurateur_id' => 'sometimes|uuid|exists:users,id'
        ]);

        $query = \App\Models\Commande::where('status', 'recuperee')
            ->whereBetween('created_at', [$request->date_debut, $request->date_fin]);

        if ($request->filled('restaurateur_id')) {
            $query->where('restaurateur_id', $request->restaurateur_id);
        }

        $commandes = $query->with(['client:id,name', 'restaurateur:id,name', 'items.plat:id,nom'])
            ->get();

        $rapport = [
            'periode' => [
                'debut' => $request->date_debut,
                'fin' => $request->date_fin
            ],
            'resume' => [
                'nombre_commandes' => $commandes->count(),
                'chiffre_affaires' => $commandes->sum('total_general'),
                'panier_moyen' => $commandes->avg('total_general'),
                'frais_livraison_total' => $commandes->sum('frais_livraison')
            ],
            'par_type_service' => [
                'livraison' => $commandes->where('type_service', 'livraison')->count(),
                'retrait' => $commandes->where('type_service', 'retrait')->count()
            ],
            'commandes' => $commandes
        ];

        return response()->json([
            'success' => true,
            'data' => $rapport
        ]);
    });

    // Rapport des plats les plus vendus
    Route::get('/bestsellers', function (Request $request) {
        $request->validate([
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date|after_or_equal:date_debut',
            'restaurateur_id' => 'sometimes|uuid|exists:users,id',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = \App\Models\CommandeItem::join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->where('commandes.status', 'recuperee')
            ->with('plat:id,nom,description,prix,image');

        if ($request->filled('date_debut')) {
            $query->whereDate('commandes.created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('commandes.created_at', '<=', $request->date_fin);
        }

        if ($request->filled('restaurateur_id')) {
            $query->where('commandes.restaurateur_id', $request->restaurateur_id);
        }

        $limit = $request->get('limit', 10);

        $bestsellers = $query->select('commande_items.plat_id')
            ->selectRaw('SUM(commande_items.quantite) as total_vendu')
            ->selectRaw('SUM(commande_items.prix_total) as chiffre_affaires')
            ->selectRaw('COUNT(DISTINCT commande_items.commande_id) as nombre_commandes')
            ->groupBy('commande_items.plat_id')
            ->orderByDesc('total_vendu')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bestsellers
        ]);
    });
});

// Routes des notifications de commande
Route::get('/notifications', [NotificationCommandeController::class, 'index']);
Route::get('/notifications/recent', [NotificationCommandeController::class, 'recent']);
Route::get('/notifications/unread-count', [NotificationCommandeController::class, 'unreadCount']);
Route::post('/notifications', [NotificationCommandeController::class, 'store']);
Route::patch('/notifications/{id}/read', [NotificationCommandeController::class, 'markAsRead']);
Route::patch('/notifications/mark-all-read', [NotificationCommandeController::class, 'markAllAsRead']);
Route::delete('/notifications/{id}', [NotificationCommandeController::class, 'destroy']);


// Routes plats publiques (sans authentification)
Route::get('plats', [PlatController::class, 'index']);

// Routes plats protégées (avec authentification)
// Route publique pour les menus du jour (clients)
Route::get('plats/today', [PlatController::class, 'publicTodayMenus'])->name('plats.today.public');

Route::middleware('auth:api')->group(function () {
    // Routes admin uniquement - Modération et statistiques
    Route::middleware('role:admin')->group(function () {
        Route::get('plats-moderation', [PlatController::class, 'moderation'])->name('plats.moderation');
        Route::put('plats/{plat}/approve', [PlatController::class, 'approve'])->name('plats.approve');
        Route::put('plats/{plat}/reject', [PlatController::class, 'reject'])->name('plats.reject');
        Route::get('plats-trashed', [PlatController::class, 'trashed']);
        Route::post('plats/{plat}/restore', [PlatController::class, 'restore']);
    });

    // Routes restaurateur - Gestion des plats
    Route::middleware('role:restaurateur,admin')->group(function () {
        Route::post('plats', [PlatController::class, 'store']);
        Route::put('plats/{plat}', [PlatController::class, 'update']);
        Route::delete('plats/{plat}', [PlatController::class, 'destroy']);
        Route::get('plats/today-admin', [PlatController::class, 'todayMenus'])->name('plats.today');
        Route::get('plats/stats', [PlatController::class, 'getStats'])->name('plats.stats');
        Route::post('plats/bulk-update-status', [PlatController::class, 'bulkUpdateStatus'])->name('plats.bulk-status');
        Route::get('plats/restaurateur/{id}', [PlatController::class, 'getByRestaurateur'])->name('plats.by-restaurateur');
    });

    // Routes accessibles par tous les utilisateurs authentifiés
    Route::get('plats/{plat}', [PlatController::class, 'show']);
});

// Routes des moyens de paiement des restaurateurs
Route::get('restaurateur-moyen-paiements', [RestaurateurMoyenPaiementController::class, 'index']);
Route::post('restaurateur-moyen-paiements', [RestaurateurMoyenPaiementController::class, 'store']);
Route::get('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'show']);
Route::put('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'update']);
Route::delete('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}', [RestaurateurMoyenPaiementController::class, 'destroy']);
Route::get('restaurateur-moyen-paiements-trashed', [RestaurateurMoyenPaiementController::class, 'trashed']);
Route::post('restaurateur-moyen-paiements/{restaurateurMoyenPaiement}/restore', [RestaurateurMoyenPaiementController::class, 'restore']);

// Routes des tarifs de livraison
Route::get('tarif-livraisons', [TarifLivraisonController::class, 'index']);
Route::post('tarif-livraisons', [TarifLivraisonController::class, 'store']);
Route::get('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'show']);
Route::put('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'update']);
Route::delete('tarif-livraisons/{tarifLivraison}', [TarifLivraisonController::class, 'destroy']);
Route::get('tarif-livraisons-trashed', [TarifLivraisonController::class, 'trashed']);
Route::post('tarif-livraisons/{tarifLivraison}/restore', [TarifLivraisonController::class, 'restore']);
