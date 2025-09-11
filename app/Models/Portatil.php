<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portatil extends Model
{
    use HasFactory;
    
    protected $fillable = ['portatil_id', 'persona_id', 'qrCode', 'marca', 'modelo'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'idPersona');
    }

    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'portatil_id', 'portatil_id');
    }
}
