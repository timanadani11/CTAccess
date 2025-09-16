<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            // documento puede ser null, pero si existe debe ser único
            $table->unique('documento');
        });

        Schema::table('portatiles', function (Blueprint $table) {
            $table->unique('qrCode');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->unique('placa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropUnique(['documento']);
        });

        Schema::table('portatiles', function (Blueprint $table) {
            $table->dropUnique(['qrCode']);
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropUnique(['placa']);
        });
    }
};
