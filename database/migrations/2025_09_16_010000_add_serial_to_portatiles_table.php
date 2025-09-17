<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('portatiles', function (Blueprint $table) {
            $table->string('serial')->nullable()->after('persona_id');
            $table->unique('serial');
        });
    }

    public function down(): void
    {
        Schema::table('portatiles', function (Blueprint $table) {
            $table->dropUnique(['serial']);
            $table->dropColumn('serial');
        });
    }
};
