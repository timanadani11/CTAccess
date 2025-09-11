<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Acceso extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'persona_id',
        'portatil_id',
        'vehiculo_id',
        'fecha_entrada',
        'fecha_salida',
        'estado',
        'usuario_entrada_id',
        'usuario_salida_id'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'idPersona');
    }

    public function portatil()
    {
        return $this->belongsTo(Portatil::class, 'portatil_id', 'portatil_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function usuarioEntrada()
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_entrada_id', 'idUsuariio');
    }

    public function usuarioSalida()
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_salida_id', 'idUsuariio');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'accesoId_id_fk');
    }
}
