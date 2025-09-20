<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class UsuarioSistema extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios_sistema';
    protected $primaryKey = 'idUsuariio';
    protected $fillable = ['UserName', 'password', 'nombre', 'rol', 'activo'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Laravel guard name convenience (optional)
    protected $guard = 'system';

    // Roles
    public const ROL_ADMIN = 'admin';
    public const ROL_CELADOR = 'celador';

    // Username field for authentication (we'll use in controller)
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    // Ensure passwords are always hashed when set
    public function setPasswordAttribute($value): void
    {
        if (! empty($value)) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    public function accesosEntrada()
    {
        return $this->hasMany(Acceso::class, 'usuario_entrada_id');
    }

    public function accesosSalida()
    {
        return $this->hasMany(Acceso::class, 'usuario_salida_id');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'usuario_id_fk');
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function isCelador(): bool
    {
        return $this->rol === self::ROL_CELADOR;
    }

    public function hasRole(string $role): bool
    {
        return $this->rol === $role;
    }

    // Scope for active users
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
