<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\NotificationCommande;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationCommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer quelques commandes et utilisateurs existants
        $commandes = Commande::all();
        $users = User::all();

        if ($commandes->isEmpty() || $users->isEmpty()) {
            return;
        }

        $types = ['order', 'user', 'payment', 'system', 'info', 'warning', 'success', 'error'];

        foreach ($commandes as $commande) {
            // Créer une notification pour chaque type possible
            foreach ($types as $type) {
                NotificationCommande::create([
                    'title' => $this->getTitre($type),
                    'message' => $this->getMessage($type, $commande->id),
                    'type' => $type,
                    'user_id' => $commande->user_id,
                    'is_read' => fake()->boolean(30), // 30% de chances d'être lu
                    'read_at' => fake()->boolean(30) ? now()->subMinutes(fake()->numberBetween(1, 60)) : null,
                    'action_required' => fake()->boolean(20), // 20% de chances de nécessiter une action
                    'data' => json_encode([
                        'commande_id' => $commande->id,
                        'amount' => fake()->randomFloat(2, 10, 200),
                        'status' => fake()->randomElement(['pending', 'processing', 'completed']),
                    ])
                ]);
            }
        }
    }

    /**
     * Obtenir le titre de la notification selon le type
     */
    private function getTitre(string $type): string
    {
        return match ($type) {
            'order' => 'Mise à jour de votre commande',
            'user' => 'Information utilisateur',
            'payment' => 'Statut du paiement',
            'system' => 'Information système',
            'info' => 'Information',
            'warning' => 'Attention',
            'success' => 'Succès',
            'error' => 'Erreur',
            default => 'Notification'
        };
    }

    /**
     * Obtenir le message de la notification selon le type
     */
    private function getMessage(string $type, string $commandeId): string
    {
        return match ($type) {
            'order' => "Mise à jour concernant votre commande #$commandeId.",
            'user' => "Des modifications ont été apportées à votre profil.",
            'payment' => "Une mise à jour du paiement de votre commande #$commandeId est disponible.",
            'system' => "Information importante du système.",
            'info' => "Information concernant votre compte.",
            'warning' => "Action requise pour votre commande #$commandeId.",
            'success' => "Votre commande #$commandeId a été traitée avec succès.",
            'error' => "Un problème est survenu avec votre commande #$commandeId.",
            default => "Nouvelle notification système"
        };
    }
}
