<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $raison = null
    ) {}

    public function build()
    {
        return $this->subject('Suspension de votre compte Lelagali')
            ->view('emails.user-suspended');
    }
}
