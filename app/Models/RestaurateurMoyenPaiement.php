<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurateurMoyenPaiement extends Model
{
    use SoftDeletes;

    protected $table = 'restaurateur_moyens_paiement';
    
    protected $fillable = [
        'restaurateur_id',
        'moyen_paiement_id',
        'numero_compte',
        'status',
    ];

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function moyenPaiement()
    {
        return $this->belongsTo(MoyenPaiement::class);
    }
}
