<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoyenPaiement extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    // public function restaurateurs()
    // {
    //     return $this->belongsToMany(User::class, 'restaurateur_moyens_paiement', 'moyen_paiement_id', 'restaurateur_id');
    // }

    // public function commandes()
    // {
    //     return $this->hasMany(Commande::class);
    // }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a créé ce moyen de paiement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec les commandes utilisant ce moyen de paiement
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'moyen_paiement_id');
    }

    /**
     * Relation avec les restaurateurs acceptant ce moyen de paiement
     */
    public function restaurateurs()
    {
        return $this->belongsToMany(User::class, 'restaurateur_moyens_paiement', 'moyen_paiement_id', 'restaurateur_id')
            ->withPivot('numero_compte')
            ->withTimestamps();
    }

    /**
     * Vérifie si ce moyen de paiement est électronique
     */
    public function getIsElectronicAttribute(): bool
    {
        $electronicMethods = [
            'mobile_money',
            'mtn_money',
            'moov_money',
            'orange_money',
            'wave',
            'carte_bancaire',
            'flooz',
            'tmoney',
            'paypal',
            'visa',
            'mastercard'
        ];

        return collect($electronicMethods)->contains(function ($method) {
            return stripos($this->nom, $method) !== false;
        });
    }

    /**
     * Vérifie si ce moyen de paiement est en espèces
     */
    public function getIsCashAttribute(): bool
    {
        $cashMethods = ['cash', 'espèces', 'liquide', 'argent'];

        return collect($cashMethods)->contains(function ($method) {
            return stripos($this->nom, $method) !== false;
        });
    }

    /**
     * Scopes
     */
    public function scopeElectronic($query)
    {
        return $query->where(function ($q) {
            $electronicMethods = [
                'mobile_money',
                'mtn_money',
                'moov_money',
                'orange_money',
                'wave',
                'carte_bancaire',
                'flooz',
                'tmoney',
                'paypal'
            ];

            foreach ($electronicMethods as $method) {
                $q->orWhere('nom', 'LIKE', "%{$method}%");
            }
        });
    }

    public function scopeCash($query)
    {
        return $query->where(function ($q) {
            $cashMethods = ['cash', 'espèces', 'liquide'];

            foreach ($cashMethods as $method) {
                $q->orWhere('nom', 'LIKE', "%{$method}%");
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
