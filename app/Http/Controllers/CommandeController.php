<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\NewOrderRestaurantMail;
use App\Mail\OrderConfirmationMail;

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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'required|uuid|exists:users,id',
                'restaurateur_id' => 'required|uuid|exists:users,id',
                'moyen_paiement_id' => 'required|uuid|exists:moyen_paiements,id',
                'type_service' => 'required|in:livraison,retrait',
                'adresse_livraison' => 'required_if:type_service,livraison|nullable|string',
                'quartier_livraison_id' => 'required_if:type_service,livraison|nullable|uuid|exists:quartiers,id',
                'notes_client' => 'nullable|string',
                'reference_paiement' => 'nullable|string|max:255',
                'numero_paiement' => 'nullable|string|max:50',
                'items' => 'required|array|min:1',
                'items.*.plat_id' => 'required|uuid|exists:plats,id',
                'items.*.quantite' => 'required|integer|min:1',
                'items.*.prix_unitaire' => 'required|integer|min:0'
            ]);

            // Validation : Vérifier que le moyen de paiement est configuré pour ce restaurateur
            $restaurateurMoyenPaiement = \App\Models\RestaurateurMoyenPaiement::where('restaurateur_id', $validated['restaurateur_id'])
                ->where('moyen_paiement_id', $validated['moyen_paiement_id'])
                ->whereNull('deleted_at')
                ->first();

            if (!$restaurateurMoyenPaiement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce moyen de paiement n\'est pas accepté par ce restaurateur'
                ], 400);
            }

            // Validation : Si livraison, vérifier que le restaurateur livre dans ce quartier
            if ($validated['type_service'] === 'livraison') {
                $tarifLivraison = \App\Models\TarifLivraison::where('restaurateur_id', $validated['restaurateur_id'])
                    ->where('quartier_id', $validated['quartier_livraison_id'])
                    ->whereNull('deleted_at')
                    ->first();

                if (!$tarifLivraison) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ce restaurateur ne livre pas dans ce quartier'
                    ], 400);
                }
            }

            // Créer la commande
            $commande = Commande::create([
                'client_id' => $validated['client_id'],
                'restaurateur_id' => $validated['restaurateur_id'],
                'moyen_paiement_id' => $validated['moyen_paiement_id'],
                'type_service' => $validated['type_service'],
                'adresse_livraison' => $validated['adresse_livraison'] ?? null,
                'quartier_livraison_id' => $validated['quartier_livraison_id'] ?? null,
                'notes_client' => $validated['notes_client'] ?? null,
                'reference_paiement' => $validated['reference_paiement'] ?? null,
                'numero_paiement' => $validated['numero_paiement'] ?? null,
                'status' => 'en_attente',
                'status_paiement' => true,
                'total_plats' => 0,
                'frais_livraison' => 0,
                'total_general' => 0
            ]);

            // Créer les items de commande
            foreach ($validated['items'] as $itemData) {
                $prixTotal = $itemData['quantite'] * $itemData['prix_unitaire'];

                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'plat_id' => $itemData['plat_id'],
                    'quantite' => $itemData['quantite'],
                    'prix_unitaire' => $itemData['prix_unitaire'],
                    'prix_total' => $prixTotal
                ]);
            }

            // Recalculer les totaux
            $commande->recalculerTotaux();

            // Calculer le temps de préparation estimé
            $tempsPreparation = $commande->calculerTempsPreparation();
            $commande->update(['temps_preparation_estime' => $tempsPreparation]);

            // Charger les relations pour les emails
            $commande->load([
                'client:id,name,phone,email',
                'restaurateur:id,name,phone,email',
                'moyenPaiement:id,nom',
                'quartierLivraison:id,nom',
                'items.plat:id,nom,prix'
            ]);

            DB::commit();

            // Envoyer l'email au restaurateur (en mode non-bloquant)
            try {
                if ($commande->restaurateur->email) {
                    Mail::to($commande->restaurateur->email)->send(new NewOrderRestaurantMail($commande));
                }

                // Envoyer l'email de confirmation au client
                if ($commande->client->email) {
                    Mail::to($commande->client->email)->send(new OrderConfirmationMail($commande));
                }
            } catch (\Exception $mailException) {
                // Logger l'erreur mais ne pas bloquer la création de commande
                Log::warning('Erreur envoi email pour commande ' . $commande->numero_commande . ': ' . $mailException->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $commande,
                'message' => 'Commande créée avec succès'
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la commande: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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

    public function confirmerPaiement($id)
    {
        try {
            $commande = Commande::with([
                'client:id,name,email',
                'restaurateur:id,name',
                'moyenPaiement:id,nom'
            ])->findOrFail($id);

            $commande->update([
                'status_paiement' => true,
                'paiement_rejete' => false,
                'raison_rejet_paiement' => null
            ]);

            return response()->json([
                'success' => true,
                'data' => $commande->fresh(),
                'message' => 'Paiement confirmé avec succès'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la confirmation du paiement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejeterPaiement(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'raison' => 'nullable|string|max:500'
            ]);

            $commande = Commande::with([
                'client:id,name,email,phone',
                'restaurateur:id,name,phone',
                'moyenPaiement:id,nom',
                'items.plat:id,nom,prix'
            ])->findOrFail($id);

            // Marquer le paiement comme rejeté (la commande reste active mais bloquée)
            $commande->update([
                'status_paiement' => false,
                'paiement_rejete' => true,
                'raison_rejet_paiement' => $validated['raison'] ?? null
            ]);

            // Envoyer l'email au client
            try {
                if ($commande->client->email) {
                    Mail::to($commande->client->email)->send(
                        new \App\Mail\PaymentRejectedMail($commande, $validated['raison'] ?? null)
                    );
                }
            } catch (\Exception $mailException) {
                Log::warning('Erreur envoi email de rejet pour commande ' . $commande->numero_commande . ': ' . $mailException->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $commande->fresh(),
                'message' => 'Paiement rejeté et client notifié'
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors du rejet du paiement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
