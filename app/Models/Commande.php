<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'status_paiement' => 'boolean',
        'temps_preparation_estime' => 'integer',
        'total_plats' => 'integer',
        'frais_livraison' => 'integer',
        'total_general' => 'integer'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function restaurateur()
    {
        return $this->belongsTo(User::class, 'restaurateur_id');
    }

    public function quartierLivraison()
    {
        return $this->belongsTo(Quartier::class, 'quartier_livraison_id');
    }

    public function moyenPaiement()
    {
        return $this->belongsTo(MoyenPaiement::class);
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationCommande::class);
    }

    // Génération automatique du numéro de commande
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->numero_commande = 'CMD-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        });
    }

    /**
     * Scopes pour faciliter les requêtes
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeConfirmee($query)
    {
        return $query->where('status', 'confirmee');
    }

    public function scopePrete($query)
    {
        return $query->where('status', 'prete');
    }

    public function scopeEnLivraison($query)
    {
        return $query->where('status', 'en_livraison');
    }

    public function scopeRecuperee($query)
    {
        return $query->where('status', 'recuperee');
    }

    public function scopeAnnulee($query)
    {
        return $query->where('status', 'annulee');
    }

    public function scopePayee($query)
    {
        return $query->where('status_paiement', true);
    }

    public function scopeNonPayee($query)
    {
        return $query->where('status_paiement', false);
    }

    public function scopeLivraison($query)
    {
        return $query->where('type_service', 'livraison');
    }

    public function scopeRetrait($query)
    {
        return $query->where('type_service', 'retrait');
    }

    public function scopeAujourdHui($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeParRestaurateur($query, $restaurateurId)
    {
        return $query->where('restaurateur_id', $restaurateurId);
    }

    public function scopeParClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Accesseurs et mutateurs
     */
    public function getTotalFormatAttribute()
    {
        return number_format($this->total_general, 0, ',', ' ') . ' FCFA';
    }

    public function getIsElectronicPaymentAttribute()
    {
        if (!$this->moyenPaiement) return false;

        $electronicMethods = [
            'telecel_money',
            'sank_money',
            'moov_money',
            'orange_money',
            'wave'
        ];

        return collect($electronicMethods)->contains(function ($method) {
            return stripos($this->moyenPaiement->nom, $method) !== false;
        });
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'prete' => 'Prête',
            'en_livraison' => 'En livraison',
            'recuperee' => 'Récupérée',
            'annulee' => 'Annulée'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getCanBeCancelledAttribute()
    {
        return !in_array($this->status, ['recuperee', 'annulee']);
    }

    public function getTempsEcouleAttribute()
    {
        $diffInMinutes = $this->created_at->diffInMinutes(now());

        if ($diffInMinutes < 1) return 'À l\'instant';
        if ($diffInMinutes < 60) return "Il y a {$diffInMinutes} min";

        $diffInHours = floor($diffInMinutes / 60);
        if ($diffInHours < 24) return "Il y a {$diffInHours}h";

        $diffInDays = floor($diffInHours / 24);
        return "Il y a {$diffInDays} jour(s)";
    }

    /**
     * Méthodes utilitaires
     */
    public function peutEtreAcceptee(): bool
    {
        return $this->status === 'en_attente';
    }

    public function peutEtrePreparee(): bool
    {
        return $this->status === 'confirmee';
    }

    public function peutEtreLivree(): bool
    {
        return $this->status === 'prete' && $this->type_service === 'livraison';
    }

    public function peutEtreRecuperee(): bool
    {
        return in_array($this->status, ['prete', 'en_livraison']);
    }

    public function peutEtreAnnulee(): bool
    {
        return !in_array($this->status, ['recuperee']);
    }

    public function accepter(): bool
    {
        if (!$this->peutEtreAcceptee()) return false;

        $this->status = 'confirmee';
        return $this->save();
    }

    public function marquerPrete(): bool
    {
        if (!$this->peutEtrePreparee()) return false;

        $this->status = 'prete';
        return $this->save();
    }

    public function mettreEnLivraison(): bool
    {
        if (!$this->peutEtreLivree()) return false;

        $this->status = 'en_livraison';
        return $this->save();
    }

    public function marquerRecuperee(): bool
    {
        if (!$this->peutEtreRecuperee()) return false;

        $this->status = 'recuperee';
        return $this->save();
    }

    public function annuler(string $raison = null): bool
    {
        if (!$this->peutEtreAnnulee()) return false;

        $this->status = 'annulee';
        if ($raison) {
            $this->raison_annulation = $raison;
        }
        return $this->save();
    }

    public function marquerPayee(string $reference = null, string $numero = null): bool
    {
        $this->status_paiement = true;
        if ($reference) $this->reference_paiement = $reference;
        if ($numero) $this->numero_paiement = $numero;

        return $this->save();
    }

    /**
     * Calculs automatiques
     */
    public function calculerTempsPreparation(): int
    {
        $tempsBase = 30; // 30 minutes par défaut
        $tempsParItem = $this->items->sum(function ($item) {
            return ($item->plat->temps_preparation ?? 15) * $item->quantite;
        });

        return max($tempsBase, $tempsParItem);
    }

    public function recalculerTotaux(): void
    {
        $this->total_plats = $this->items->sum('prix_total');

        if ($this->type_service === 'livraison') {
            // Calculer les frais de livraison selon le quartier
            $this->frais_livraison = $this->calculerFraisLivraison();
        } else {
            $this->frais_livraison = 0;
        }

        $this->total_general = $this->total_plats + $this->frais_livraison;
        $this->save();
    }

    private function calculerFraisLivraison(): int
    {
        // Logique de calcul des frais de livraison
        // Peut être basée sur le quartier, la distance, etc.
        return 500; // Frais fixes pour l'exemple
    }
}
