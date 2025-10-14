<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsuarioSistema;

class UsuarioSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        UsuarioSistema::updateOrCreate(
            ['UserName' => 'admin'],
            [
                'password' => 'admin12345', // Se hasheará por el mutator del modelo
                'nombre' => 'Administrador General',
                'tipo_documento' => 'DNI',
                'documento' => '12345678',
                'correo' => 'admin@ctaccess.com',
                'activo' => true,
            ]
        );
    }
}
