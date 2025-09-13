<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurateurMoyenPaiement extends Model
{
    use SoftDeletes;

    protected $table = 'restaurateur_moyens_paiement';

    protected $guarded = ['id'];

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function moyenPaiement()
    {
        return $this->belongsTo(MoyenPaiement::class);
    }
}
