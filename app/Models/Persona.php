<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Persona extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $primaryKey = 'idPersona';
    protected $fillable = ['documento', 'Nombre', 'TipoPersona', 'qrCode', 'correo', 'contraseña'];
    
    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'contraseña' => 'hashed',
        ];
    }

    // Override para usar 'correo' en lugar de 'email'
    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    // Override para usar 'contraseña' en lugar de 'password'
    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // Método para verificar contraseña
    public function checkPassword($password)
    {
        return Hash::check($password, $this->contraseña);
    }

    public function getRouteKeyName(): string
    {
        return 'idPersona';
    }

    public function portatiles()
    {
        return $this->hasMany(Portatiles::class, 'persona_id');
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'persona_id');
    }

    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'persona_id');
    }
}
