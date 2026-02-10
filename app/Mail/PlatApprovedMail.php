<?php

namespace App\Mail;

use App\Models\Plat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlatApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Plat $plat
    ) {}

    public function build()
    {
        return $this->subject('Votre plat a été approuvé sur Lelagali')
            ->view('emails.plat-approved');
    }
}
