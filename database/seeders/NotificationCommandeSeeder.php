<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\NotificationCommande;
use Illuminate\Database\Seeder;

class NotificationCommandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer quelques commandes existantes
        $commandes = Commande::all();

        if ($commandes->isEmpty()) {
            return;
        }

        $types = ['order', 'user', 'payment', 'system', 'info', 'warning', 'success', 'error'];

        foreach ($commandes as $index => $commande) {
            // Créer une notification pour chaque type possible
            foreach ($types as $typeIndex => $type) {
                NotificationCommande::create([
                    'title' => $this->getTitre($type),
                    'message' => $this->getMessage($type, $commande->id),
                    'type' => $type,
                    'user_id' => $commande->client_id ?? $commande->restaurateur_id, // dépend de ta structure
                    'is_read' => $typeIndex % 2 === 0, // une sur deux est lue
                    'read_at' => $typeIndex % 2 === 0 ? now()->subMinutes(10 * ($index + 1)) : null,
                    'action_required' => $typeIndex % 3 === 0, // une sur trois nécessite une action
                    'data' => json_encode([
                        'commande_id' => $commande->id,
                        'amount' => 100.00 + ($index * 50), // montant déterministe
                        'status' => $this->getStatus($typeIndex),
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

    /**
     * Retourner un statut déterministe en fonction de l’index
     */
    private function getStatus(int $index): string
    {
        $statuses = ['pending', 'processing', 'completed'];
        return $statuses[$index % count($statuses)];
    }
}
