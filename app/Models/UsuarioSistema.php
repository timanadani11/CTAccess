<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioSistema extends Model
{
    use HasFactory;

    protected $table = 'usuarios_sistema';
    protected $primaryKey = 'idUsuariio';
    protected $fillable = ['UserName', 'password', 'nombre', 'rol', 'activo'];

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
}
