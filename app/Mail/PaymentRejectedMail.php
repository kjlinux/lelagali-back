<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Commande $commande,
        public ?string $raison = null
    ) {}

    public function build()
    {
        return $this->subject('Paiement non confirmé - ' . $this->commande->numero_commande)
            ->view('emails.payment-rejected');
    }
}
