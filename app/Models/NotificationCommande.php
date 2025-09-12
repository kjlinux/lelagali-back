<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationCommande extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commande_id',
        'user_id', // destinataire
        'type', // 'nouvelle_commande', 'status_change', 'paiement_confirme', etc.
        'titre',
        'message',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

