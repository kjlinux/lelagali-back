<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledByClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Commande $commande
    ) {}

    public function build()
    {
        return $this->subject('Commande annulée - ' . $this->commande->numero_commande)
            ->view('emails.order-cancelled-by-client');
    }
}
