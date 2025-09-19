<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'email_verified_at',
        'profile_image',
        'address',
        'quartier_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'client_id');
    }

    public function plats()
    {
        return $this->hasMany(Plat::class, 'restaurateur_id');
    }

    public function restaurateurPaiements()
    {
        return $this->belongsToMany(MoyenPaiement::class, 'restaurateur_moyens_paiement', 'restaurateur_id', 'moyen_paiement_id');
    }

    public function tarifLivraisons()
    {
        return $this->hasMany(TarifLivraison::class, 'restaurateur_id');
    }
}
