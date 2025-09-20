<?php

namespace App\Models;

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
