<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendario', function (Blueprint $table) {
            $table->dropColumn('pessoa'); // Remove o antigo
            $table->json('participantes')->nullable(); // Adiciona o novo que aceita array
        });
    }

    public function down(): void
    {
        Schema::table('calendario', function (Blueprint $table) {
            $table->dropColumn('participantes');
            $table->string('pessoa')->nullable();
        });
    }
};
