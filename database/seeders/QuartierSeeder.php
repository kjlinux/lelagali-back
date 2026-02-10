<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuartierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->first()->id;

        // Quartiers populaires de Ouagadougou
        $quartiers = [
            'Ouaga 2000',
            'Gounghin',
            'Dapoya',
            'Cissin',
            'Zone du Bois',
            'Secteur 4',
            'Secteur 15',
            'Secteur 30',
            'Kalgondin',
            'Tanghin',
            'Paspanga',
            'Somgandé',
            'Pissy',
            'Koulouba',
            'Tampouy',
            'Samandin',
            'Balkuy',
            'Bogodogo',
            'Baskuy',
            'Sig-Noghin'
        ];

        foreach ($quartiers as $quartier) {
            DB::table('quartiers')->insert([
                'id' => Str::uuid(),
                'nom' => $quartier,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
