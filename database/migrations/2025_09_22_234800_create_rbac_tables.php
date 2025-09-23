<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // roles table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });
        }

        // permisos table
        if (!Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('modulo')->nullable();
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });
        }

        // rol_permiso pivot
        if (!Schema::hasTable('rol_permiso')) {
            Schema::create('rol_permiso', function (Blueprint $table) {
                $table->unsignedBigInteger('rol_id');
                $table->unsignedBigInteger('permiso_id');

                $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permiso_id')->references('id')->on('permisos')->onDelete('cascade');

                $table->primary(['rol_id', 'permiso_id']);
            });
        }

        // usuario_rol pivot (for usuarios_sistema)
        // Nota: la PK de usuarios_sistema es 'idUsuariio'
        if (!Schema::hasTable('usuario_rol')) {
            Schema::create('usuario_rol', function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id');
                $table->unsignedBigInteger('rol_id');

                $table->foreign('usuario_id')->references('idUsuariio')->on('usuarios_sistema')->onDelete('cascade');
                $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');

                $table->primary(['usuario_id', 'rol_id']);
            });
        }
    }

    public function down(): void
    {
        // Drop pivots first due to FKs
        if (Schema::hasTable('usuario_rol')) {
            Schema::drop('usuario_rol');
        }
        if (Schema::hasTable('rol_permiso')) {
            Schema::drop('rol_permiso');
        }
        if (Schema::hasTable('permisos')) {
            Schema::drop('permisos');
        }
        if (Schema::hasTable('roles')) {
            Schema::drop('roles');
        }
    }
};
