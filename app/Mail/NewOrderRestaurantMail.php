<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderRestaurantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Commande $commande
    ) {}

    public function build()
    {
        return $this->subject('Nouvelle commande reçue - ' . $this->commande->numero_commande)
            ->view('emails.new-order-restaurant');
    }
}
