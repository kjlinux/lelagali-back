<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoyenPaiement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'description',
        'status',
        'icon',
        'created_by',
    ];

    public function restaurateurs()
    {
        return $this->belongsToMany(User::class, 'restaurateur_moyens_paiement', 'moyen_paiement_id', 'restaurateur_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
