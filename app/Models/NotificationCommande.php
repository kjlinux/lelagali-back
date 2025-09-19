<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationCommande extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
