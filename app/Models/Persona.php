<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'idPersona';
    protected $fillable = ['documento', 'Nombre', 'TipoPersona', 'Foto'];

    public function getRouteKeyName(): string
    {
        return 'idPersona';
    }

    public function portatiles()
    {
        return $this->hasMany(Portatil::class, 'persona_id');
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
