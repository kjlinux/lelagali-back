<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\MoyenPaiement;
use App\Models\Plat;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération des utilisateurs nécessaires
        $clients = User::where('role', 'client')->take(4)->get();
        $restaurateurs = User::where('role', 'restaurateur')->take(3)->get();

        // Récupération des moyens de paiement et quartiers
        $moyensPaiement = MoyenPaiement::all();
        $quartiers = Quartier::all();

        // Liste des statuts possibles (fixes pour l’exemple)
        $statutsCommande = ['en_attente', 'confirmee', 'prete', 'en_livraison', 'recuperee', 'annulee'];

        foreach ($restaurateurs as $index => $restaurateur) {
            // Créer 3 plats fixes pour ce restaurateur
            $plats = collect([
                Plat::create([
                    'restaurateur_id' => $restaurateur->id,
                    'nom' => "Plat spécial $restaurateur->id - 1",
                    'prix' => 5000,
                ]),
                Plat::create([
                    'restaurateur_id' => $restaurateur->id,
                    'nom' => "Plat spécial $restaurateur->id - 2",
                    'prix' => 7500,
                ]),
                Plat::create([
                    'restaurateur_id' => $restaurateur->id,
                    'nom' => "Plat spécial $restaurateur->id - 3",
                    'prix' => 10000,
                ]),
            ]);

            // Nombre de commandes
            $nombreCommandes = $index === 0 ? 3 : 2;

            for ($i = 0; $i < $nombreCommandes; $i++) {
                $client = $clients[$i % $clients->count()];
                $quartierLivraison = $quartiers[$i % $quartiers->count()];

                // Items fixes : 2 plats avec quantités prédéfinies
                $itemsCommande = [
                    [
                        'plat_id' => $plats[0]->id,
                        'quantite' => 1,
                        'prix_unitaire' => $plats[0]->prix,
                        'prix_total' => $plats[0]->prix * 1,
                    ],
                    [
                        'plat_id' => $plats[1]->id,
                        'quantite' => 2,
                        'prix_unitaire' => $plats[1]->prix,
                        'prix_total' => $plats[1]->prix * 2,
                    ],
                ];

                // Totaux
                $totalPlats = collect($itemsCommande)->sum('prix_total');
                $typeService = $i % 2 === 0 ? 'livraison' : 'retrait';
                $fraisLivraison = $typeService === 'livraison' ? 1000 : 0;
                $totalGeneral = $totalPlats + $fraisLivraison;

                // Statut et paiement fixes
                $status = $statutsCommande[$i % count($statutsCommande)];
                $statusPaiement = $i % 2 === 0; // alternance payé / non payé

                // Champs additionnels
                $referencePaiement = $statusPaiement ? 'PAY-ABC123' : null;
                $numeroPaiement = $statusPaiement ? 'TRX123456' : null;
                $notesClient = $i % 2 === 0 ? "Merci pour ce repas" : null;
                $notesRestaurateur = $i % 3 === 0 ? "Préparé rapidement" : null;
                $raisonAnnulation = $status === 'annulee' ? "Client indisponible" : null;

                // Création de la commande
                $commande = Commande::create([
                    'client_id' => $client->id,
                    'restaurateur_id' => $restaurateur->id,
                    'type_service' => $typeService,
                    'adresse_livraison' => $typeService === 'livraison' ? "Rue du marché, Abidjan" : null,
                    'quartier_livraison_id' => $quartierLivraison->id,
                    'moyen_paiement_id' => $moyensPaiement->first()->id,
                    'status' => $status,
                    'status_paiement' => $statusPaiement,
                    'reference_paiement' => $referencePaiement,
                    'numero_paiement' => $numeroPaiement,
                    'notes_client' => $notesClient,
                    'notes_restaurateur' => $notesRestaurateur,
                    'raison_annulation' => $raisonAnnulation,
                    'temps_preparation_estime' => 30,
                    'total_plats' => $totalPlats,
                    'frais_livraison' => $fraisLivraison,
                    'total_general' => $totalGeneral,
                ]);

                // Création des items liés
                foreach ($itemsCommande as $item) {
                    CommandeItem::create([
                        'commande_id' => $commande->id,
                        'plat_id' => $item['plat_id'],
                        'quantite' => $item['quantite'],
                        'prix_unitaire' => $item['prix_unitaire'],
                        'prix_total' => $item['prix_total'],
                    ]);
                }
            }
        }
    }
}
