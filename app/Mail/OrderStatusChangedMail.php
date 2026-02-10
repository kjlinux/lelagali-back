<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Commande $commande,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function build()
    {
        $statusLabels = [
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'prete' => 'Prête',
            'en_livraison' => 'En livraison',
            'recuperee' => 'Livrée',
            'annulee' => 'Annulée'
        ];

        $subject = 'Mise à jour de votre commande - ' . ($statusLabels[$this->newStatus] ?? $this->newStatus);

        return $this->subject($subject)
            ->view('emails.order-status-changed');
    }
}
