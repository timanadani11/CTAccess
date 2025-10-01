<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incidencia extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'incidenciaId';
    protected $fillable = ['accesoId_id_fk', 'usuario_id_fk', 'tipo', 'descripcion'];

    public function acceso()
    {
        return $this->belongsTo(Acceso::class, 'accesoId_id_fk');
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_id_fk', 'idUsuario');
    }
}
