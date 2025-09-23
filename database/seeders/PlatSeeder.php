<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurateurs = User::where('role', 'restaurateur')->get();

        if ($restaurateurs->isEmpty()) {
            $this->command->warn('Aucun restaurateur trouvé. Veuillez d\'abord exécuter UserSeeder.');
            return;
        }

        $platsData = [
            // Plats ivoiriens traditionnels
            [
                'nom' => 'Attiéké Poisson Braisé',
                'description' => 'Attiéké accompagné de poisson frais braisé aux épices locales, servi avec une sauce tomate pimentée',
                'prix' => 2500,
                'quantite_disponible' => 15,
                'temps_preparation' => 25
            ],
            [
                'nom' => 'Riz Gras au Poulet',
                'description' => 'Riz parfumé cuit dans un bouillon riche, accompagné de morceaux de poulet tendre et de légumes',
                'prix' => 3000,
                'quantite_disponible' => 20,
                'temps_preparation' => 30
            ],
            [
                'nom' => 'Foutou Banane Sauce Arachide',
                'description' => 'Foutou de banane plantain accompagné d\'une sauce arachide onctueuse avec viande de bœuf',
                'prix' => 2800,
                'quantite_disponible' => 12,
                'temps_preparation' => 35
            ],
            [
                'nom' => 'Alloco Sauce Tomate',
                'description' => 'Bananes plantains frites dorées servies avec une sauce tomate épicée et œuf dur',
                'prix' => 1500,
                'quantite_disponible' => 25,
                'temps_preparation' => 15
            ],
            [
                'nom' => 'Kedjenou de Poulet',
                'description' => 'Poulet mijoté aux légumes dans une poterie traditionnelle, un plat authentique ivoirien',
                'prix' => 3500,
                'quantite_disponible' => 10,
                'temps_preparation' => 45
            ],
            [
                'nom' => 'Garba',
                'description' => 'Attiéké mélangé au thon, tomates, oignons et piment, un plat populaire de rue',
                'prix' => 1200,
                'quantite_disponible' => 30,
                'temps_preparation' => 10
            ],
            [
                'nom' => 'Sauce Gombo au Poisson Fumé',
                'description' => 'Sauce gombo traditionnelle avec poisson fumé, servie avec foutou d\'igname',
                'prix' => 2700,
                'quantite_disponible' => 18,
                'temps_preparation' => 40
            ],
            [
                'nom' => 'Riz au Gras de Mouton',
                'description' => 'Riz cuisiné dans un bouillon de mouton avec légumes et épices, très savoureux',
                'prix' => 3200,
                'quantite_disponible' => 15,
                'temps_preparation' => 35
            ],
            [
                'nom' => 'Placali Sauce Graine',
                'description' => 'Pâte de manioc (placali) accompagnée de sauce graine palmiste avec poisson et viande',
                'prix' => 2600,
                'quantite_disponible' => 14,
                'temps_preparation' => 30
            ],
            [
                'nom' => 'Attieké Poisson Chat',
                'description' => 'Attiéké servi avec poisson chat grillé et sa sauce pimentée traditionnelle',
                'prix' => 2400,
                'quantite_disponible' => 20,
                'temps_preparation' => 20
            ],
            [
                'nom' => 'Tchêp Djen',
                'description' => 'Riz rouge sénégalais adapté au goût ivoirien, avec poisson et légumes variés',
                'prix' => 2900,
                'quantite_disponible' => 16,
                'temps_preparation' => 40
            ],
            [
                'nom' => 'Sauce Claire aux Épinards',
                'description' => 'Sauce légère aux épinards avec morceaux de viande, servie avec banane bouillie',
                'prix' => 2200,
                'quantite_disponible' => 22,
                'temps_preparation' => 25
            ],
            [
                'nom' => 'Ragout d\'Igname',
                'description' => 'Igname coupée en morceaux, mijotée dans une sauce tomate avec viande de bœuf',
                'prix' => 2500,
                'quantite_disponible' => 18,
                'temps_preparation' => 35
            ],
            [
                'nom' => 'Thieboudienne',
                'description' => 'Plat de riz au poisson avec légumes, inspiré de la cuisine ouest-africaine',
                'prix' => 3100,
                'quantite_disponible' => 12,
                'temps_preparation' => 45
            ],
            [
                'nom' => 'Sauce Aubergine',
                'description' => 'Sauce d\'aubergines avec poisson fumé et bœuf, accompagnée de riz blanc',
                'prix' => 2300,
                'quantite_disponible' => 19,
                'temps_preparation' => 30
            ],
            // Plats rapides et collations
            [
                'nom' => 'Sandwich Jambon Fromage',
                'description' => 'Pain frais avec jambon, fromage, tomate, laitue et sauce mayonnaise',
                'prix' => 1800,
                'quantite_disponible' => 35,
                'temps_preparation' => 8
            ],
            [
                'nom' => 'Salade de Fruits Tropicaux',
                'description' => 'Mélange de fruits frais locaux : ananas, mangue, papaye, banane, orange',
                'prix' => 1000,
                'quantite_disponible' => 25,
                'temps_preparation' => 5
            ],
            [
                'nom' => 'Omelette aux Légumes',
                'description' => 'Omelette garnie de tomates, oignons, poivrons, servie avec pain frais',
                'prix' => 1400,
                'quantite_disponible' => 40,
                'temps_preparation' => 12
            ],
            [
                'nom' => 'Jus de Bissap Frais',
                'description' => 'Boisson rafraîchissante à base d\'hibiscus, légèrement sucrée et parfumée',
                'prix' => 500,
                'quantite_disponible' => 50,
                'temps_preparation' => 2
            ],
            [
                'nom' => 'Beignets Haricots (Kosseh)',
                'description' => 'Beignets croustillants à base de haricots noirs, spécialité locale',
                'prix' => 800,
                'quantite_disponible' => 30,
                'temps_preparation' => 15
            ]
        ];

        foreach ($restaurateurs as $restaurateur) {
            // Chaque restaurateur aura entre 8 et 15 plats
            $nombrePlats = rand(8, 15);
            $platsSelectionnes = collect($platsData)->random($nombrePlats);

            foreach ($platsSelectionnes as $platData) {
                // Créer des plats pour aujourd'hui, demain et après-demain
                for ($i = 0; $i < 3; $i++) {
                    $dateDisponibilite = Carbon::today()->addDays($i);

                    // Variation des quantités et prix selon les jours
                    $quantiteBase = $platData['quantite_disponible'];
                    $prixBase = $platData['prix'];

                    // Réduire légèrement les quantités pour les jours suivants
                    if ($i > 0) {
                        $quantiteBase = max(5, $quantiteBase - rand(2, 8));
                    }

                    // Variation légère du prix (±10%)
                    $variationPrix = rand(-10, 10);
                    $prixFinal = $prixBase + ($prixBase * $variationPrix / 100);

                    Plat::create([
                        'id' => Str::uuid(),
                        'nom' => $platData['nom'],
                        'description' => $platData['description'],
                        'prix' => round($prixFinal),
                        'quantite_disponible' => $quantiteBase,
                        'quantite_vendue' => rand(0, min(5, $quantiteBase)),
                        'restaurateur_id' => $restaurateur->id,
                        'date_disponibilite' => $dateDisponibilite,
                        'is_approved' => rand(0, 10) > 1, // 90% des plats sont approuvés
                        'approved_by' => User::where('role', 'admin')->first()?->id,
                        'approved_at' => rand(0, 10) > 1 ? now()->subHours(rand(1, 48)) : null,
                        'temps_preparation' => $platData['temps_preparation']
                    ]);
                }
            }
        }

        $this->command->info('Plats créés avec succès pour tous les restaurateurs.');
    }
}
