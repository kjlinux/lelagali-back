<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quartier;
use App\Models\TarifLivraison;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TarifLivraisonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurateurs = User::where('role', 'restaurateur')->get();
        $quartiers = Quartier::all();

        if ($restaurateurs->isEmpty()) {
            $this->command->warn('Aucun restaurateur trouvé. Veuillez d\'abord exécuter UserSeeder.');
            return;
        }

        if ($quartiers->isEmpty()) {
            $this->command->warn('Aucun quartier trouvé. Veuillez d\'abord exécuter QuartierSeeder.');
            return;
        }

        // Tarifs de base par quartier (certains quartiers sont plus chers à livrer)
        $tarifsBase = [
            'Cocody' => 1500,
            'Plateau' => 1200,
            'Adjamé' => 1000,
            'Yopougon' => 1800,
            'Marcory' => 1300,
            'Koumassi' => 1600,
            'Port-Bouët' => 2000,
            'Attécoubé' => 1400,
            'Abobo' => 1700,
            'Treichville' => 1100,
            'Bingerville' => 2200,
            'Anyama' => 2500,
            'Grand-Bassam' => 3000,
            'Songon' => 1900,
            'Riviera' => 1600
        ];

        foreach ($restaurateurs as $restaurateur) {
            foreach ($quartiers as $quartier) {
                // Prix de base selon le quartier
                $prixBase = $tarifsBase[$quartier->nom] ?? 1500;

                // Variation selon le restaurateur (±20%)
                $variation = rand(-20, 20);
                $prixFinal = $prixBase + ($prixBase * $variation / 100);

                // Arrondir au multiple de 50 le plus proche
                $prixFinal = round($prixFinal / 50) * 50;

                // Prix minimum de 500 FCFA
                $prixFinal = max(500, $prixFinal);

                TarifLivraison::create([
                    'id' => Str::uuid(),
                    'restaurateur_id' => $restaurateur->id,
                    'quartier_id' => $quartier->id,
                    'prix' => $prixFinal
                ]);
            }
        }

        $this->command->info('Tarifs de livraison créés avec succès pour tous les restaurateurs et quartiers.');
    }
}
