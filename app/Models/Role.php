<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'rol_permiso', 'rol_id', 'permiso_id');
    }

    public function usuariosSistema()
    {
        // Relación muchos a muchos con usuarios del sistema
        return $this->belongsToMany(UsuarioSistema::class, 'usuario_rol', 'rol_id', 'usuario_id');
    }
}
