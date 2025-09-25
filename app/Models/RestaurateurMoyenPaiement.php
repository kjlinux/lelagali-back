<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurateurMoyenPaiement extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'restaurateur_moyens_paiement';

    protected $guarded = ['id'];

    /**
     * Relation avec le restaurateur (User)
     */
    public function restaurateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    /**
     * Relation avec le moyen de paiement
     */
    public function moyenPaiement(): BelongsTo
    {
        return $this->belongsTo(MoyenPaiement::class, 'moyen_paiement_id');
    }

    /**
     * Scope pour filtrer par restaurateur
     */
    public function scopeForRestaurateur($query, $restaurateurId)
    {
        return $query->where('restaurateur_id', $restaurateurId);
    }

    /**
     * Scope pour les moyens de paiement actifs (non supprimés)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Vérifie si ce moyen de paiement nécessite un numéro de compte
     */
    public function needsAccountNumber(): bool
    {
        if (!$this->moyenPaiement) {
            return true; // Par sécurité, on assume que c'est nécessaire
        }

        $nom = strtolower($this->moyenPaiement->nom);

        // Les espèces ne nécessitent pas de numéro de compte
        return !str_contains($nom, 'espèces') && !str_contains($nom, 'cash');
    }

    /**
     * Retourne le type de paiement basé sur le nom
     */
    public function getPaymentTypeAttribute(): string
    {
        if (!$this->moyenPaiement) {
            return 'unknown';
        }

        $nom = strtolower($this->moyenPaiement->nom);

        if (str_contains($nom, 'orange') || str_contains($nom, 'mtn') || str_contains($nom, 'money')) {
            return 'mobile_money';
        }

        if (str_contains($nom, 'espèces') || str_contains($nom, 'cash')) {
            return 'cash';
        }

        if (str_contains($nom, 'visa') || str_contains($nom, 'mastercard') || str_contains($nom, 'card')) {
            return 'card';
        }

        return 'other';
    }

    /**
     * Validation personnalisée lors de la sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Vérifier qu'un numéro de compte est fourni si nécessaire
            if ($model->needsAccountNumber() && empty($model->numero_compte)) {
                throw new \InvalidArgumentException(
                    'Un numéro de compte est requis pour ce moyen de paiement'
                );
            }

            // Si pas de nom de titulaire, utiliser le nom du restaurateur
            if (empty($model->nom_titulaire) && $model->restaurateur) {
                $model->nom_titulaire = $model->restaurateur->name;
            }
        });

        static::creating(function ($model) {
            // Vérifier l'unicité : un restaurateur ne peut pas avoir le même moyen de paiement deux fois
            $existing = static::where('restaurateur_id', $model->restaurateur_id)
                ->where('moyen_paiement_id', $model->moyen_paiement_id)
                ->first();

            if ($existing) {
                throw new \InvalidArgumentException(
                    'Ce moyen de paiement est déjà configuré pour ce restaurateur'
                );
            }
        });
    }
}
