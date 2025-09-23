<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios_sistema')) {
            return;
        }

        if (Schema::hasColumn('usuarios_sistema', 'rol')) {
            Schema::table('usuarios_sistema', function (Blueprint $table) {
                $table->dropColumn('rol');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('usuarios_sistema')) {
            return;
        }

        if (!Schema::hasColumn('usuarios_sistema', 'rol')) {
            Schema::table('usuarios_sistema', function (Blueprint $table) {
                $table->string('rol')->nullable();
            });
        }
    }
};
