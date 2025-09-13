<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function quartierLivraison()
    {
        return $this->belongsTo(Quartier::class, 'quartier_livraison_id');
    }

    public function moyenPaiement()
    {
        return $this->belongsTo(MoyenPaiement::class);
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationCommande::class);
    }

    // Génération automatique du numéro de commande
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->numero_commande = 'CMD-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        });
    }
}
