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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->date('data_pedido');
            $table->date('data_entrega_prevista')->nullable();
            $table->enum('status', ['orcamento', 'aberto', 'em_producao', 'finalizado', 'entregue'])->default('aberto');
            $table->text('observacoes')->nullable();
            $table->boolean('pago')->default(false);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->integer('dias_entrega')->default(7);
            
            // Campos para os arquivos
            $table->string('comprovante_path')->nullable();
            $table->string('comprovante_mime_type')->nullable();
            $table->string('comprovante_original_name')->nullable();
            $table->unsignedBigInteger('comprovante_size')->nullable();
            
            $table->string('contrato_path')->nullable();
            $table->string('contrato_mime_type')->nullable();
            $table->string('contrato_original_name')->nullable();
            $table->unsignedBigInteger('contrato_size')->nullable();
            
            $table->string('outros_arquivos_path')->nullable();
            $table->string('outros_arquivos_mime_type')->nullable();
            $table->string('outros_arquivos_original_name')->nullable();
            $table->unsignedBigInteger('outros_arquivos_size')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
