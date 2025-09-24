<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quartier extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tarifLivraisons()
    {
        return $this->hasMany(TarifLivraison::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec les commandes livrées dans ce quartier
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'quartier_livraison_id');
    }

    /**
     * Relation avec les tarifs de livraison pour ce quartier
     */
    public function tarifsLivraison(): HasMany
    {
        return $this->hasMany(TarifLivraison::class, 'quartier_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeAvecLivraison($query)
    {
        return $query->whereHas('tarifsLivraison');
    }
}
