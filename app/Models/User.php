<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'promotion_id', 'role', 'points'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    // ----------------------------------------------------- Relations

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }

    public function appelsIa(): HasMany
    {
        return $this->hasMany(AppelIa::class);
    }

    // -------------------------------------------------------- Rôles

    public function estEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    public function estDelegue(): bool
    {
        return $this->role === 'delegue';
    }
    public function appelsIaAujourdhui(): int
{
    return $this->appelsIa()
        ->where('created_at', '>=', now()->startOfDay())
        ->count();
}

public function quotaIaRestant(): int
{
    return max(0, config('cohorte.quota_ia_quotidien') - $this->appelsIaAujourdhui());
}

public function peutAppelerIa(): bool
{
    return $this->quotaIaRestant() > 0;
}
}