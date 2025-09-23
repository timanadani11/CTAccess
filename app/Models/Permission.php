<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $fillable = [
        'nombre',
        'modulo',
        'descripcion',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rol_permiso', 'permiso_id', 'rol_id');
    }
}
