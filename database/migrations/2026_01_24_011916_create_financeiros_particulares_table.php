<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('financeiros_particulares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Vincula ao usuário (Bernardo/Gabriele)

            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');

            $table->string('tipo'); // despesa, receita
            $table->string('categoria'); // Casa, Lazer, etc
            $table->string('responsavel'); // Bernardo, Gabriele, Ambos

            $table->boolean('pago')->default(false);

            // Campos de controle de parcelas/recorrência
            $table->boolean('is_fixo')->default(false);
            $table->boolean('is_parcelado')->default(false);
            $table->integer('parcela_atual')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->uuid('grupo_id')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Opcional: para lixeira
        });
    }

    public function down()
    {
        Schema::dropIfExists('financeiros_particulares');
    }
};
