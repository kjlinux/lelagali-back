<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plat extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'prix' => 'decimal:0',
        'date_disponibilite' => 'date',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Ajouter l'URL complète de l'image lors de la sérialisation
     */
    protected $appends = ['image_url'];

    /**
     * Obtenir l'URL complète de l'image
     * Fonctionne avec S3 ou local storage
     */
    public function getImageUrlAttribute(): ?string
    {
        return StorageHelper::getUrl($this->image);
    }

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope pour les plats du jour suivant
    public function scopeForTomorrow($query)
    {
        return $query->where('date_disponibilite', now()->addDay()->toDateString());
    }

    // Scope pour les plats disponibles
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where('is_approved', true)
            ->where('quantite_disponible', '>', 0);
    }
}
