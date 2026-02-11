<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer d'abord un quartier
        $quartierId = Str::uuid();
        DB::table('quartiers')->insert([
            'id' => $quartierId,
            'nom' => 'Ouaga 2000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin
        $adminId = Str::uuid();
        DB::table('users')->insert([
            'id' => $adminId,
            'name' => 'Administrateur Principal',
            'email' => 'admin@lelagali.bf',
            'phone' => '070000001',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
            'profile_image' => null,
            'address' => 'Avenue Kwame Nkrumah, Ouagadougou',
            'quartier_id' => $quartierId,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mettre à jour le quartier avec created_by
        DB::table('quartiers')
            ->where('id', $quartierId)
            ->update(['created_by' => $adminId]);

    //     // Clients burkinabè
    //     $clientsData = [
    //         ['name' => 'Ouédraogo Aminata', 'email' => 'aminata.ouedraogo@gmail.com', 'phone' => '070123456', 'address' => 'Secteur 15, Ouagadougou'],
    //         ['name' => 'Sawadogo Ibrahim', 'email' => 'ibrahim.sawadogo@yahoo.fr', 'phone' => '070234567', 'address' => 'Secteur 30, Ouagadougou'],
    //         ['name' => 'Traoré Fatimata', 'email' => 'fatimata.traore@gmail.com', 'phone' => '070345678', 'address' => 'Gounghin, Ouagadougou'],
    //         ['name' => 'Compaoré Boukary', 'email' => 'boukary.compaore@hotmail.com', 'phone' => '070456789', 'address' => 'Dapoya, Ouagadougou'],
    //         ['name' => 'Kaboré Salimata', 'email' => 'salimata.kabore@gmail.com', 'phone' => '070567890', 'address' => 'Cissin, Ouagadougou'],
    //     ];

    //     foreach ($clientsData as $client) {
    //         DB::table('users')->insert([
    //             'id' => Str::uuid(),
    //             'name' => $client['name'],
    //             'email' => $client['email'],
    //             'phone' => $client['phone'],
    //             'email_verified_at' => now(),
    //             'password' => Hash::make('password'),
    //             'role' => 'client',
    //             'active' => true,
    //             'profile_image' => null,
    //             'address' => $client['address'],
    //             'quartier_id' => $quartierId,
    //             'remember_token' => Str::random(10),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     // Restaurateurs burkinabè
    //     $restaurateursData = [
    //         ['name' => 'Restaurant Le Verdoyant', 'email' => 'contact@leverdoyant.bf', 'phone' => '025301010', 'address' => 'Avenue Charles de Gaulle, Ouagadougou'],
    //         ['name' => 'Chez Tantine', 'email' => 'cheztantine@gmail.com', 'phone' => '025302020', 'address' => 'Zone du Bois, Ouagadougou'],
    //         ['name' => 'Le Palmier d\'Or', 'email' => 'contact@palmieredor.bf', 'phone' => '025303030', 'address' => 'Ouaga 2000, Ouagadougou'],
    //         ['name' => 'Chez Damas', 'email' => 'chezdamas@yahoo.fr', 'phone' => '025304040', 'address' => 'Secteur 4, Ouagadougou'],
    //     ];

    //     foreach ($restaurateursData as $resto) {
    //         DB::table('users')->insert([
    //             'id' => Str::uuid(),
    //             'name' => $resto['name'],
    //             'email' => $resto['email'],
    //             'phone' => $resto['phone'],
    //             'email_verified_at' => now(),
    //             'password' => Hash::make('password'),
    //             'role' => 'restaurateur',
    //             'active' => true,
    //             'profile_image' => null,
    //             'address' => $resto['address'],
    //             'quartier_id' => $quartierId,
    //             'remember_token' => Str::random(10),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     // On attend que les quartiers soient créés
    //     $quartiers = DB::table('quartiers')->pluck('id')->toArray();

    //     // Clients supplémentaires avec quartiers aléatoires
    //     $moreClientsData = [
    //         ['name' => 'Zoungrana Moussa', 'email' => 'moussa.zoungrana@gmail.com', 'phone' => '070678901', 'address' => 'Kalgondin, Ouagadougou'],
    //         ['name' => 'Nikiéma Rasmata', 'email' => 'rasmata.nikiema@yahoo.fr', 'phone' => '070789012', 'address' => 'Tanghin, Ouagadougou'],
    //     ];

    //     foreach ($moreClientsData as $client) {
    //         DB::table('users')->insert([
    //             'id' => Str::uuid(),
    //             'name' => $client['name'],
    //             'email' => $client['email'],
    //             'phone' => $client['phone'],
    //             'email_verified_at' => now(),
    //             'password' => Hash::make('password'),
    //             'role' => 'client',
    //             'active' => true,
    //             'profile_image' => null,
    //             'address' => $client['address'],
    //             'quartier_id' => $quartiers[array_rand($quartiers)],
    //             'remember_token' => Str::random(10),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     // Restaurateurs supplémentaires avec quartiers aléatoires
    //     $moreRestaurantsData = [
    //         ['name' => 'La Terrasse', 'email' => 'contact@laterrasse.bf', 'phone' => '025305050', 'address' => 'Boulevard Tensoba, Ouagadougou'],
    //         ['name' => 'Maquis du Coin', 'email' => 'maquisducoin@gmail.com', 'phone' => '025306060', 'address' => 'Paspanga, Ouagadougou'],
    //     ];

    //     foreach ($moreRestaurantsData as $resto) {
    //         DB::table('users')->insert([
    //             'id' => Str::uuid(),
    //             'name' => $resto['name'],
    //             'email' => $resto['email'],
    //             'phone' => $resto['phone'],
    //             'email_verified_at' => now(),
    //             'password' => Hash::make('password'),
    //             'role' => 'restaurateur',
    //             'active' => true,
    //             'profile_image' => null,
    //             'address' => $resto['address'],
    //             'quartier_id' => $quartiers[array_rand($quartiers)],
    //             'remember_token' => Str::random(10),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
        // }
    }
}
