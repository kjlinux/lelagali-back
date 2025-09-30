<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\MoyenPaiement;
use App\Models\Plat;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Database\Seeder;
// SUPPRIMÉ : use Faker\Factory as Faker;

class CommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utiliser le helper fake() de Laravel au lieu de Faker\Factory
        $faker = fake('fr_FR');

        // Récupération des utilisateurs nécessaires
        $clients = User::where('role', 'client')->take(4)->get();
        $restaurateurs = User::where('role', 'restaurateur')->take(3)->get();

        // Récupération des moyens de paiement et quartiers
        $moyensPaiement = MoyenPaiement::all();
        $quartiers = Quartier::all();

        // Liste des statuts possibles
        $statutsCommande = ['en_attente', 'confirmee', 'prete', 'en_livraison', 'recuperee', 'annulee'];

        // Pour chaque restaurateur
        foreach ($restaurateurs as $index => $restaurateur) {

            // Créer des plats pour ce restaurateur
            $plats = Plat::factory()
                ->count(3)
                ->create([
                    'restaurateur_id' => $restaurateur->id,
                    'prix' => $faker->numberBetween(2000, 15000),
                ]);

            // Nombre de commandes pour ce restaurateur
            $nombreCommandes = $index === 0 ? 3 : 2; // 3 pour le premier, 2 pour les autres

            // Création des commandes
            for ($i = 0; $i < $nombreCommandes; $i++) {
                $client = $clients->random();
                $quartierLivraison = $quartiers->random();

                // Préparer les items de la commande
                $nombreItems = $faker->numberBetween(1, 3);
                $itemsCommande = [];
                $totalPlats = 0;

                for ($j = 0; $j < $nombreItems; $j++) {
                    $plat = $plats->random();
                    $quantite = $faker->numberBetween(1, 3);
                    $prixUnitaire = $plat->prix;
                    $prixTotal = $prixUnitaire * $quantite;

                    $itemsCommande[] = [
                        'plat_id' => $plat->id,
                        'quantite' => $quantite,
                        'prix_unitaire' => $prixUnitaire,
                        'prix_total' => $prixTotal,
                    ];

                    $totalPlats += $prixTotal;
                }

                // Calcul des totaux
                $typeService = $faker->randomElement(['livraison', 'retrait']);
                $fraisLivraison = $typeService === 'livraison' ? 1000 : 0;
                $totalGeneral = $totalPlats + $fraisLivraison;

                // Statut et paiement
                $status = $faker->randomElement($statutsCommande);
                $statusPaiement = $faker->boolean(); // true = payé, false = non payé

                // Champs additionnels demandés
                $referencePaiement = $statusPaiement ? 'PAY-' . strtoupper($faker->lexify('??????')) : null;
                $numeroPaiement = $statusPaiement ? 'TRX' . $faker->numberBetween(100000, 999999) : null;
                $notesClient = $faker->boolean(60) ? $faker->sentence() : null; // 60% de chance d'avoir une note
                $notesRestaurateur = $faker->boolean(40) ? $faker->sentence() : null; // 40% de chance
                $raisonAnnulation = $status === 'annulee' ? $faker->sentence() : null;

                // Création de la commande
                $commande = Commande::create([
                    'client_id' => $client->id,
                    'restaurateur_id' => $restaurateur->id,
                    'type_service' => $typeService,
                    'adresse_livraison' => $typeService === 'livraison' ? $faker->address() : null,
                    'quartier_livraison_id' => $quartierLivraison->id,
                    'moyen_paiement_id' => $moyensPaiement->random()->id,
                    'status' => $status,
                    'status_paiement' => $statusPaiement,
                    'reference_paiement' => $referencePaiement,
                    'numero_paiement' => $numeroPaiement,
                    'notes_client' => $notesClient,
                    'notes_restaurateur' => $notesRestaurateur,
                    'raison_annulation' => $raisonAnnulation,
                    'temps_preparation_estime' => $faker->numberBetween(15, 60),
                    'total_plats' => $totalPlats,
                    'frais_livraison' => $fraisLivraison,
                    'total_general' => $totalGeneral,
                ]);

                // Création des items liés à la commande
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
