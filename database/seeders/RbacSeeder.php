<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UsuarioSistema;

class RbacSeeder extends Seeder
{
    /**
     * Seed the application's database for RBAC.
     */
    public function run(): void
    {
        // Define a baseline set of permissions (extend as needed)
        $permissions = [
            ['nombre' => 'personas.view',      'modulo' => 'personas',  'descripcion' => 'Ver personas'],
            ['nombre' => 'personas.create',    'modulo' => 'personas',  'descripcion' => 'Crear personas'],
            ['nombre' => 'personas.update',    'modulo' => 'personas',  'descripcion' => 'Editar personas'],
            ['nombre' => 'personas.delete',    'modulo' => 'personas',  'descripcion' => 'Eliminar personas'],

            ['nombre' => 'accesos.view',       'modulo' => 'accesos',   'descripcion' => 'Ver accesos'],
            ['nombre' => 'accesos.create',     'modulo' => 'accesos',   'descripcion' => 'Crear accesos'],
            ['nombre' => 'accesos.update',     'modulo' => 'accesos',   'descripcion' => 'Editar accesos'],

            ['nombre' => 'incidencias.view',   'modulo' => 'incidencias','descripcion' => 'Ver incidencias'],
            ['nombre' => 'incidencias.create', 'modulo' => 'incidencias','descripcion' => 'Crear incidencias'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['nombre' => $perm['nombre']], $perm);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['nombre' => 'administrador'],
            ['descripcion' => 'Administrador con todos los permisos']
        );

        $celadorRole = Role::firstOrCreate(
            ['nombre' => 'celador'],
            ['descripcion' => 'Rol operativo para control de accesos']
        );

        // Assign all permissions to admin
        $allPerms = Permission::all();
        $adminRole->permissions()->syncWithoutDetaching($allPerms->pluck('id'));

        // Optionally, a minimal set for celador
        $celadorPerms = Permission::whereIn('nombre', [
            'accesos.view', 'accesos.create', 'incidencias.view', 'incidencias.create'
        ])->get();
        $celadorRole->permissions()->syncWithoutDetaching($celadorPerms->pluck('id'));

        // Attach admin role to existing admin user if present
        $adminUser = UsuarioSistema::where('UserName', 'admin')->first();
        if ($adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
            if (empty($adminUser->rol_principal_id)) {
                $adminUser->rol_principal_id = $adminRole->id;
                $adminUser->save();
            }
        }

        // Attach celador role to existing celador user if present
        $celadorUser = UsuarioSistema::where('UserName', 'celador')->first();
        if ($celadorUser) {
            $celadorUser->roles()->syncWithoutDetaching([$celadorRole->id]);
            if (empty($celadorUser->rol_principal_id)) {
                $celadorUser->rol_principal_id = $celadorRole->id;
                $celadorUser->save();
            }
        }
    }
}
