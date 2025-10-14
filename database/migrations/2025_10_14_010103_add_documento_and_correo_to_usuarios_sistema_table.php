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
        Schema::table('usuarios_sistema', function (Blueprint $table) {
            $table->string('tipo_documento', 20)->nullable()->after('nombre');
            $table->string('documento', 50)->nullable()->unique()->after('tipo_documento');
            $table->string('correo', 100)->nullable()->unique()->after('documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios_sistema', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento', 'documento', 'correo']);
        });
    }
};
