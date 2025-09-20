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

        // Pour chaque restaurateur
        foreach ($restaurateurs as $index => $restaurateur) {
            // Créer des plats pour ce restaurateur
            $plats = Plat::factory()->count(3)->create([
                'restaurateur_id' => $restaurateur->id,
                'prix' => fake()->numberBetween(2000, 15000)
            ]);

            // Nombre de commandes pour ce restaurateur
            $nombreCommandes = $index === 0 ? 3 : 2; // 3 commandes pour le premier, 2 pour les autres

            // Créer les commandes
            for ($i = 0; $i < $nombreCommandes; $i++) {
                $client = $clients->random();
                $quartierLivraison = $quartiers->random();

                // Préparer les items de la commande
                $nombreItems = fake()->numberBetween(1, 3);
                $itemsCommande = [];
                $totalPlats = 0;

                for ($j = 0; $j < $nombreItems; $j++) {
                    $plat = $plats->random();
                    $quantite = fake()->numberBetween(1, 3);
                    $prixUnitaire = $plat->prix;
                    $prixTotal = $prixUnitaire * $quantite;

                    $itemsCommande[] = [
                        'plat' => $plat,
                        'quantite' => $quantite,
                        'prix_unitaire' => $prixUnitaire,
                        'prix_total' => $prixTotal
                    ];

                    $totalPlats += $prixTotal;
                }

                // Calculer les totaux avant de créer la commande
                $typeService = fake()->randomElement(['livraison', 'retrait']);
                $fraisLivraison = $typeService === 'livraison' ? 1000 : 0;
                $totalGeneral = $totalPlats + $fraisLivraison;

                // Créer la commande avec tous les totaux
                $commande = Commande::create([
                    'client_id' => $client->id,
                    'restaurateur_id' => $restaurateur->id,
                    'type_service' => $typeService,
                    'adresse_livraison' => fake()->address(),
                    'quartier_livraison_id' => $quartierLivraison->id,
                    'moyen_paiement_id' => $moyensPaiement->random()->id,
                    'status' => fake()->randomElement(['en_attente', 'confirmee', 'prete', 'en_livraison', 'recuperee']),
                    'status_paiement' => fake()->boolean(),
                    'temps_preparation_estime' => fake()->numberBetween(15, 60),
                    'total_plats' => $totalPlats,
                    'frais_livraison' => $fraisLivraison,
                    'total_general' => $totalGeneral
                ]);

                // Créer les items de la commande
                foreach ($itemsCommande as $item) {
                    CommandeItem::create([
                        'commande_id' => $commande->id,
                        'plat_id' => $item['plat']->id,
                        'quantite' => $item['quantite'],
                        'prix_unitaire' => $item['prix_unitaire'],
                        'prix_total' => $item['prix_total']
                    ]);
                }
            }
        }
    }
}
