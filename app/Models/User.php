<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'activo'            => 'boolean',
    ];

    // Solo superadmin accede al panel Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->rol === 'superadmin' && $this->activo;
    }

    public function esSuperAdmin(): bool { return $this->rol === 'superadmin'; }
    public function esDueno(): bool      { return $this->rol === 'dueno'; }
    public function esAuxiliar(): bool   { return $this->rol === 'auxiliar'; }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}