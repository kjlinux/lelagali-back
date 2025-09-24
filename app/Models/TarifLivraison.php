<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifLivraison extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'prix' => 'decimal:0',
    ];

    // public function restaurateur()
    // {
    //     return $this->belongsTo(User::class, 'restaurateur_id');
    // }

    // public function quartier()
    // {
    //     return $this->belongsTo(Quartier::class);
    // }


    /**
     * Relation avec le restaurateur
     */
    public function restaurateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    /**
     * Relation avec le quartier
     */
    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'quartier_id');
    }

    /**
     * Accesseurs
     */
    public function getPrixFormatAttribute(): string
    {
        return number_format($this->prix, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Scopes
     */
    public function scopeParRestaurateur($query, $restaurateurId)
    {
        return $query->where('restaurateur_id', $restaurateurId);
    }

    public function scopeParQuartier($query, $quartierId)
    {
        return $query->where('quartier_id', $quartierId);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
