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
            'nom' => 'Quartier Central',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin
        $adminId = Str::uuid();
        DB::table('users')->insert([
            'id' => $adminId,
            'name' => 'Admin User',
            'email' => 'admin@app.com',
            'phone' => '0100000001',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
            'profile_image' => null,
            'address' => 'Adresse admin',
            'quartier_id' => $quartierId,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mettre à jour le quartier avec created_by
        DB::table('quartiers')
            ->where('id', $quartierId)
            ->update(['created_by' => $adminId]);

        // On attend que les quartiers soient créés
        $quartiers = DB::table('quartiers')->pluck('id')->toArray();

        // Clients avec quartiers aléatoires
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'name' => 'Client One',
            'email' => 'client1@app.com',
            'phone' => '0100000002',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'client',
            'active' => true,
            'profile_image' => null,
            'address' => 'Adresse client 1',
            'quartier_id' => $quartiers[array_rand($quartiers)],
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'name' => 'Client Two',
            'email' => 'client2@app.com',
            'phone' => '0100000003',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'client',
            'active' => true,
            'profile_image' => null,
            'address' => 'Adresse client 2',
            'quartier_id' => $quartiers[array_rand($quartiers)],
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Restaurateurs avec quartiers aléatoires
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'name' => 'Restaurateur One',
            'email' => 'resto1@app.com',
            'phone' => '0100000004',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'restaurateur',
            'active' => true,
            'profile_image' => null,
            'address' => 'Adresse restaurateur 1',
            'quartier_id' => $quartiers[array_rand($quartiers)],
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'id' => Str::uuid(),
            'name' => 'Restaurateur Two',
            'email' => 'resto2@app.com',
            'phone' => '0100000005',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'restaurateur',
            'active' => true,
            'profile_image' => null,
            'address' => 'Adresse restaurateur 2',
            'quartier_id' => $quartiers[array_rand($quartiers)],
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
