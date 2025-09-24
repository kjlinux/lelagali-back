<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeItem extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'prix_unitaire' => 'decimal:0',
        'prix_total' => 'decimal:0',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            // Calculer automatiquement le prix total
            if (!$model->prix_total && $model->prix_unitaire && $model->quantite) {
                $model->prix_total = $model->prix_unitaire * $model->quantite;
            }
        });

        static::updating(function ($model) {
            // Recalculer le prix total lors de la mise à jour
            if ($model->isDirty(['prix_unitaire', 'quantite'])) {
                $model->prix_total = $model->prix_unitaire * $model->quantite;
            }
        });
    }

    /**
     * Relation avec la commande
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    /**
     * Relation avec le plat
     */
    public function plat(): BelongsTo
    {
        return $this->belongsTo(Plat::class, 'plat_id');
    }

    /**
     * Accesseurs
     */
    public function getPrixTotalFormatAttribute()
    {
        return number_format($this->prix_total, 0, ',', ' ') . ' FCFA';
    }

    public function getPrixUnitaireFormatAttribute()
    {
        return number_format($this->prix_unitaire, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Scopes
     */
    public function scopeParCommande($query, $commandeId)
    {
        return $query->where('commande_id', $commandeId);
    }

    public function scopeParPlat($query, $platId)
    {
        return $query->where('plat_id', $platId);
    }
}
