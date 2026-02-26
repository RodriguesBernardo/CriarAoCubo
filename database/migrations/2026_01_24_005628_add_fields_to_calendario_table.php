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
        Schema::table('calendario', function (Blueprint $table) {
            $table->string('pessoa')->nullable(); 
            $table->string('grupo_id')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('calendario', function (Blueprint $table) {
            $table->dropColumn(['pessoa', 'grupo_id']);
        });
    }
};
