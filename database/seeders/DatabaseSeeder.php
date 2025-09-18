<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsuarioSistemaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Si deseas sembrar usuarios del modelo User, asegúrate de tener la migración de la tabla 'users'.
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Usuarios del sistema (admin y celador)
        $this->call(UsuarioSistemaSeeder::class);
        
        // Personas de prueba para autenticación
        $this->call(PersonaSeeder::class);
    }
}
