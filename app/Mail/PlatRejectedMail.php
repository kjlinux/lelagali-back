<?php

namespace App\Mail;

use App\Models\Plat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlatRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Plat $plat,
        public ?string $raison = null
    ) {}

    public function build()
    {
        return $this->subject('Votre plat a été rejeté sur Lelagali')
            ->view('emails.plat-rejected');
    }
}
