<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            QuartierSeeder::class,
            MoyenPaiementSeeder::class,
            // RestaurateurMoyenPaiementSeeder::class,
            // TarifLivraisonSeeder::class,
            // PlatSeeder::class,
            // CommandeSeeder::class,
            // NotificationCommandeSeeder::class,
        ]);
    }
}
