<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incidencia extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'incidenciaId';
    protected $table = 'incidencias';
    protected $fillable = ['accesoId_id_fk', 'usuario_id_fk', 'tipo', 'descripcion', 'prioridad'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function acceso()
    {
        return $this->belongsTo(Acceso::class, 'accesoId_id_fk', 'id');
    }

    public function reportadoPor()
    {
        return $this->belongsTo(UsuarioSistema::class, 'usuario_id_fk', 'idUsuario');
    }

    // Alias para compatibilidad
    public function usuario()
    {
        return $this->reportadoPor();
    }
}
