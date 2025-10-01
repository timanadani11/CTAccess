<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios_sistema')) {
            return;
        }

        // Add nullable FK column if not exists
        if (!Schema::hasColumn('usuarios_sistema', 'rol_principal_id')) {
            Schema::table('usuarios_sistema', function (Blueprint $table) {
                $table->unsignedBigInteger('rol_principal_id')->nullable()->after('rol');

                // We add the FK only if roles table exists at this point
                if (Schema::hasTable('roles')) {
                    $table->foreign('rol_principal_id')
                          ->references('id')->on('roles')
                          ->nullOnDelete();
                }

                $table->index('rol_principal_id', 'usuarios_sistema_rol_principal_idx');
            });
        }

        // Backfill: map legacy string column 'rol' to roles.nombre
        if (Schema::hasTable('roles') && Schema::hasColumn('usuarios_sistema', 'rol') && Schema::hasColumn('usuarios_sistema', 'rol_principal_id')) {
            // Ensure roles exist for known legacy values
            $map = [
                'admin'   => 'administrador',
                'celador' => 'celador',
            ];

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

            $roles = DB::table('roles')->whereIn('nombre', array_values($map))->pluck('id', 'nombre');

            $usuarios = DB::table('usuarios_sistema')->select('idUsuario as id', 'rol', 'rol_principal_id')->get();
            foreach ($usuarios as $u) {
                if ($u->rol_principal_id) continue; // don't override existing
                if (empty($u->rol)) continue;
                if (!isset($map[$u->rol])) continue;
                $roleName = $map[$u->rol];
                $roleId = $roles[$roleName] ?? null;
                if (!$roleId) continue;

                DB::table('usuarios_sistema')
                    ->where('idUsuario', $u->id)
                    ->update(['rol_principal_id' => $roleId]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('usuarios_sistema')) {
            return;
        }
        // Drop FK and column if exists
        if (Schema::hasColumn('usuarios_sistema', 'rol_principal_id')) {
            Schema::table('usuarios_sistema', function (Blueprint $table) {
                // Drop index and FK defensively
                try { $table->dropForeign(['rol_principal_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex('usuarios_sistema_rol_principal_idx'); } catch (\Throwable $e) {}
                $table->dropColumn('rol_principal_id');
            });
        }
    }
};
