<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandeItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commande_id',
        'plat_id',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'notes',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:0',
        'prix_total' => 'decimal:0',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function plat()
    {
        return $this->belongsTo(Plat::class);
    }
}
