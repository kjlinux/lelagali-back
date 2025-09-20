<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plat>
 */
class PlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $restaurateur = User::where('role', 'restaurateur')->inRandomOrder()->first();
        $admin = User::where('role', 'admin')->first();

        return [
            'id' => fake()->uuid(),
            'nom' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'prix' => fake()->numberBetween(500, 10000), // Entre 5€ et 100€ (en centimes)
            'quantite_disponible' => fake()->numberBetween(10, 50),
            'quantite_vendue' => fake()->numberBetween(0, 20),
            'image' => null,
            'restaurateur_id' => $restaurateur?->id,
            'date_disponibilite' => fake()->dateTimeBetween('tomorrow', '+1 week')->format('Y-m-d'),
            'is_approved' => true,
            'approved_by' => $admin?->id,
            'approved_at' => now(),
            'temps_preparation' => fake()->numberBetween(15, 120), // Entre 15 minutes et 2 heures
        ];
    }

    /**
     * Indique que le plat n'est pas encore approuvé.
     */
    public function nonApprouve(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Définit une quantité disponible spécifique.
     */
    public function quantite(int $quantite): static
    {
        return $this->state(fn(array $attributes) => [
            'quantite_disponible' => $quantite,
        ]);
    }
}
