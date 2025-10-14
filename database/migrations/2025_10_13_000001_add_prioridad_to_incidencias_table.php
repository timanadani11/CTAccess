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
        if (!Schema::hasTable('incidencias')) {
            return;
        }

        if (!Schema::hasColumn('incidencias', 'prioridad')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->enum('prioridad', ['baja', 'media', 'alta'])
                      ->default('baja')
                      ->after('descripcion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('incidencias')) {
            return;
        }

        if (Schema::hasColumn('incidencias', 'prioridad')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->dropColumn('prioridad');
            });
        }
    }
};
