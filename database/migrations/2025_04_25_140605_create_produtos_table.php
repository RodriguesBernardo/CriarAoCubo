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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->decimal('preco', 10, 2);
            $table->decimal('preco_custo', 10, 2);
            $table->decimal('custo_estimado_por_hora', 10, 2);
            $table->decimal('custo_estimado_por_grama', 10, 2);
            $table->decimal('custo_estimado_energia', 10, 2);
            $table->integer('dias_entrega')->default(7);
            $table->integer('quantidade');
            $table->string('imagem')->nullable();
            $table->string('tempo_impressao')->nullable();
            $table->string('arquivo_stl')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
