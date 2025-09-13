<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriePlat extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function plats()
    {
        return $this->hasMany(Plat::class, 'categorie_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
