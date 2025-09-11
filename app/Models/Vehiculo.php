<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehiculo extends Model
{
    use HasFactory;
    
    protected $fillable = ['persona_id', 'tipo', 'placa'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'idPersona');
    }

    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'vehiculo_id');
    }
}
