<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\MoyenPaiement;
use Illuminate\Database\Seeder;
use App\Models\RestaurateurMoyenPaiement;
use App\Models\RestaurateurMoyensPaiement;

class RestaurateurMoyenPaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurateurs = User::where('role', 'restaurateur')->get();
        $moyensPaiement = MoyenPaiement::all();

        if ($restaurateurs->isEmpty()) {
            $this->command->warn('Aucun restaurateur trouvé. Veuillez d\'abord exécuter UserSeeder.');
            return;
        }

        if ($moyensPaiement->isEmpty()) {
            $this->command->warn('Aucun moyen de paiement trouvé. Veuillez d\'abord exécuter MoyenPaiementSeeder.');
            return;
        }

        // Numéros de comptes fictifs pour les différents moyens de paiement
        $numerosFictifs = [
            'Orange Money' => [
                '07 01 23 45 67', '07 12 34 56 78', '07 23 45 67 89', '07 34 56 78 90',
                '05 45 67 89 01', '05 56 78 90 12', '05 67 89 01 23', '05 78 90 12 34'
            ],
            'MTN Mobile Money' => [
                '05 01 23 45 67', '05 12 34 56 78', '05 23 45 67 89', '05 34 56 78 90',
                '06 45 67 89 01', '06 56 78 90 12', '06 67 89 01 23', '06 78 90 12 34'
            ],
            'Wave' => [
                '01 23 45 67 89', '12 34 56 78 90', '23 45 67 89 01', '34 56 78 90 12',
                '45 67 89 01 23', '56 78 90 12 34', '67 89 01 23 45', '78 90 12 34 56'
            ],
            'Espèces' => [
                'CASH-001', 'CASH-002', 'CASH-003', 'CASH-004',
                'CASH-005', 'CASH-006', 'CASH-007', 'CASH-008'
            ]
        ];

        foreach ($restaurateurs as $index => $restaurateur) {
            // Chaque restaurateur accepte au moins 2 moyens de paiement, maximum 4
            $nombreMoyens = rand(2, 4);
            $moyensSelectionnes = $moyensPaiement->random($nombreMoyens);

            foreach ($moyensSelectionnes as $moyen) {
                // Sélectionner un numéro de compte approprié
                $numerosDisponibles = $numerosFictifs[$moyen->nom] ?? ['N/A'];
                $numeroCompte = $numerosDisponibles[$index % count($numerosDisponibles)];

                // Pour les espèces, pas besoin de numéro de compte
                if ($moyen->nom === 'Espèces') {
                    $numeroCompte = null;
                }

                RestaurateurMoyenPaiement::create([
                    'id' => Str::uuid(),
                    'restaurateur_id' => $restaurateur->id,
                    'moyen_paiement_id' => $moyen->id,
                    'numero_compte' => $numeroCompte
                ]);
            }
        }

        $this->command->info('Moyens de paiement assignés avec succès à tous les restaurateurs.');
    }
}
