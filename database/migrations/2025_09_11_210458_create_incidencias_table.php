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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id('incidenciaId');
            $table->foreignId('accesoId_id_fk')->constrained('accesos', 'id')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('usuario_id_fk')->constrained('usuarios_sistema', 'idUsuariio')->onDelete('no action')->onUpdate('no action');
            $table->string('tipo');
            $table->string('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
