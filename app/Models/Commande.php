<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_commande',
        'client_id',
        'restaurateur_id',
        'total_plats',
        'frais_livraison',
        'total_general',
        'type_commande',
        'type_service',
        'adresse_livraison',
        'quartier_livraison_id',
        'moyen_paiement_id',
        'status', // 'en_attente', 'confirmee', 'en_preparation', 'prete', 'en_livraison', 'livree', 'annulee'
        'status_paiement',
        'date_commande',
        'heure_souhaitee',
        'notes_client',
        'notes_restaurateur',
        'temps_preparation_estime',
        'heure_prete',
        'heure_livraison',
    ];

    protected $casts = [
        'total_plats' => 'decimal:0',
        'frais_livraison' => 'decimal:0',
        'total_general' => 'decimal:0',
        'date_commande' => 'datetime',
        'heure_souhaitee' => 'datetime',
        'heure_prete' => 'datetime',
        'heure_livraison' => 'datetime',
    ];

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
