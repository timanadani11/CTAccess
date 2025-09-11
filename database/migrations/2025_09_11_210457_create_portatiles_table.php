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
        Schema::create('portatiles', function (Blueprint $table) {
            $table->id('portatil_id');
            $table->foreignId('persona_id')->constrained('personas', 'idPersona')->onDelete('no action')->onUpdate('no action');
            $table->string('qrCode');
            $table->string('marca');
            $table->string('modelo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portatiles');
    }
};
