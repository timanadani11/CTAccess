<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear personas de prueba con contraseñas
        Persona::create([
            'documento' => '12345678',
            'Nombre' => 'Juan Pérez',
            'TipoPersona' => 'Empleado',
            'correo' => 'juan@empresa.com',
            'contraseña' => Hash::make('password123'),
            'qrCode' => 'QR_' . uniqid(),
        ]);

        Persona::create([
            'documento' => '87654321',
            'Nombre' => 'María García',
            'TipoPersona' => 'Visitante',
            'correo' => 'maria@visitante.com',
            'contraseña' => Hash::make('password123'),
            'qrCode' => 'QR_' . uniqid(),
        ]);

        Persona::create([
            'documento' => '11223344',
            'Nombre' => 'Carlos López',
            'TipoPersona' => 'Contratista',
            'correo' => 'carlos@contratista.com',
            'contraseña' => Hash::make('password123'),
            'qrCode' => 'QR_' . uniqid(),
        ]);

        Persona::create([
            'documento' => '44332211',
            'Nombre' => 'Ana Rodríguez',
            'TipoPersona' => 'Empleado',
            'correo' => 'ana@empresa.com',
            'contraseña' => Hash::make('password123'),
            'qrCode' => 'QR_' . uniqid(),
        ]);
    }
}
