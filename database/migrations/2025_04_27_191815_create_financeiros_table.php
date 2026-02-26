<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financeiro', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data');
            $table->enum('tipo', ['receita', 'despesa', 'retirado']);
            $table->string('categoria');
            $table->text('observacoes')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->integer('parcela_atual')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->boolean('pago')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financeiro');
    }
};