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

        DB::table('quartiers')->insert([
            [
                'id' => Str::uuid(),
                'nom' => 'Quartier Central',
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'nom' => 'Quartier Nord',
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
