<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifLivraison extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'prix' => 'decimal:0',
    ];

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }
}
