<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    /** Solo los admins pueden acceder al panel de Filament (/admin) */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            // Desvincular fichas y quitar verificación
            Ficha::where('user_id', $user->id)->update([
                'user_id'     => null,
                'verified_at' => null,
            ]);
        });
    }

    /** El negocio que gestiona este usuario (si tiene uno asignado) */
    public function ficha()
    {
        return $this->hasOne(Ficha::class);
    }
}
