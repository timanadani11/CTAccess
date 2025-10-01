<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas', 'idPersona')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('portatil_id')->nullable()->constrained('portatiles', 'portatil_id')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos', 'id')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('fecha_entrada');
            $table->timestamp('fecha_salida')->nullable();
            $table->string('estado');
            $table->foreignId('usuario_entrada_id')->constrained('usuarios_sistema', 'idUsuario')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('usuario_salida_id')->nullable()->constrained('usuarios_sistema', 'idUsuario')->onDelete('no action')->onUpdate('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesos');
    }
};
