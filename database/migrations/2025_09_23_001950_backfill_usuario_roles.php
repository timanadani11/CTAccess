<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure required tables exist before proceeding
        if (!Schema::hasTable('usuarios_sistema') || !Schema::hasTable('roles') || !Schema::hasTable('usuario_rol')) {
            return;
        }

        // Map legacy usuarios_sistema.rol values to roles.nombre
        $map = [
            'admin'   => 'administrador',
            'celador' => 'celador',
        ];

        // Ensure target roles exist
        foreach (array_values($map) as $roleName) {
            $exists = DB::table('roles')->where('nombre', $roleName)->exists();
            if (! $exists) {
                DB::table('roles')->insert([
                    'nombre' => $roleName,
                    'descripcion' => ucfirst($roleName),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Fetch role ids
        $roles = DB::table('roles')->whereIn('nombre', array_values($map))->pluck('id', 'nombre');

        // Backfill usuario_rol from usuarios_sistema.rol
        $usuarios = DB::table('usuarios_sistema')->select('idUsuario as id', 'rol')->get();
        foreach ($usuarios as $u) {
            if (empty($u->rol)) continue;
            if (!isset($map[$u->rol])) continue;
            $roleName = $map[$u->rol];
            $roleId = $roles[$roleName] ?? null;
            if (!$roleId) continue;

            // Insert if not exists
            $exists = DB::table('usuario_rol')
                ->where('usuario_id', $u->id)
                ->where('rol_id', $roleId)
                ->exists();
            if (! $exists) {
                DB::table('usuario_rol')->insert([
                    'usuario_id' => $u->id,
                    'rol_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // This data backfill is safe to be a no-op on rollback
    }
};
