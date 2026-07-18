<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password', 'role_id', 'activo', 'telefono', 'direccion', 'avatar', 'otp_code', 'otp_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
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
            'activo' => 'boolean',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(5);
        $this->save();

        return $otp;
    }

    public function verifyOtp(string $code): bool
    {
        if (! is_string($this->otp_code) || trim($this->otp_code) === '' || ! $this->otp_expires_at) {
            return false;
        }

        if (now()->gt($this->otp_expires_at)) {
            return false;
        }

        return hash_equals($this->otp_code, $code);
    }

    public function clearOtp(): void
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\Role');
    }

    public function paciente(): HasOne
    {
        return $this->hasOne('App\\Models\\Paciente');
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role?->nombre, $roles, true);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
}
