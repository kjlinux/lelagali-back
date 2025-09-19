<?php

namespace Database\Seeders;

use App\Models\MoyenPaiement;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoyenPaiementSeeder extends Seeder
{
    /**
     * Seed les moyens de paiement courants en Côte d'Ivoire.
     */
    public function run(): void
    {
        // Récupérer un admin pour created_by
        $admin = User::where('role', 'admin')->first();

        // Liste des moyens de paiement
        $moyensPaiement = [
            [
                'nom' => 'Orange Money',
                'icon' => 'orange-money.png'
            ],
            [
                'nom' => 'MTN Mobile Money',
                'icon' => 'mtn-momo.png'
            ],
            [
                'nom' => 'Moov Money',
                'icon' => 'moov-money.png'
            ],
            [
                'nom' => 'Wave',
                'icon' => 'wave.png'
            ],
            [
                'nom' => 'Espèces',
                'icon' => 'cash.png'
            ],
            [
                'nom' => 'Carte Bancaire',
                'icon' => 'credit-card.png'
            ]
        ];

        foreach ($moyensPaiement as $moyen) {
            MoyenPaiement::create([
                'nom' => $moyen['nom'],
                'icon' => $moyen['icon'],
                'created_by' => $admin->id
            ]);
        }
    }
}
