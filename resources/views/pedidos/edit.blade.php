@extends('layouts.app')

@section('title', 'Editar Pedido')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
<li class="breadcrumb-item active" aria-current="page">Editar Pedido #{{ $pedido->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Pedido #{{ $pedido->id }}
                    </h5>
                </div>
                
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6>Por favor, corrija os seguintes erros:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" enctype="multipart/form-data" id="pedidoForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Seção de Informações Básicas -->
                            <div class="col-md-6">
                                <div class="card mb-4 border-primary">
                                    <div class="card-header">
                                        <h6 class="mb-0 text-primary">Informações do Pedido</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                            <select class="form-select select2" id="cliente_id" name="cliente_id" required>
                                                <option value="">Selecione um cliente</option>
                                                @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $pedido->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                                    {{ $cliente->nome }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="data_pedido" class="form-label">Data do Pedido</label>
                                            <input type="datetime-local" class="form-control" id="data_pedido" name="data_pedido" 
                                                   value="{{ old('data_pedido', $pedido->data_pedido->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="dias_entrega" class="form-label">Dias para Entrega</label>
                                            <input type="number" class="form-control" id="dias_entrega" 
                                                name="dias_entrega" value="{{ old('dias_entrega', $pedido->dias_entrega ?? 7) }}"
                                                min="1">
                                        </div>

                                        <div class="mb-3">
                                            <label for="data_entrega_prevista" class="form-label">Data de Entrega Prevista</label>
                                            <input type="datetime-local" class="form-control" id="data_entrega_prevista" 
                                                name="data_entrega_prevista" 
                                                value="{{ old('data_entrega_prevista', optional($pedido->data_entrega_prevista)->format('Y-m-d\TH:i')) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="orcamento" {{ old('status', $pedido->status) == 'orcamento' ? 'selected' : '' }}>Orçamento</option>
                                                <option value="aberto" {{ old('status', $pedido->status) == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                                <option value="em_producao" {{ old('status', $pedido->status) == 'em_producao' ? 'selected' : '' }}>Em Produção</option>
                                                <option value="finalizado" {{ old('status', $pedido->status) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                                <option value="entregue" {{ old('status', $pedido->status) == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 form-check form-switch">
                                        <input type="hidden" name="pago" value="0">
                                            <input type="checkbox" name="pago" id="pago" 
                                                class="form-check-input" role="switch" 
                                                value="1"
                                                {{ old('pago', $pedido->pago) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pago">
                                                <i class="fas fa-check-circle me-1"></i> Pedido pago
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="desconto" class="form-label">Desconto Geral (R$)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="desconto" 
                                                   name="desconto" value="{{ old('desconto', $pedido->desconto) }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="valor_total" class="form-label">Valor Total com Desconto</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="valor_total" 
                                                   name="valor_total" value="{{ old('valor_total', $pedido->valor_total) }}" readonly>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">Observações</label>
                                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $pedido->observacoes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Seção de Arquivos -->
                                <div class="card mb-4 border-primary">
                                    <div class="card-header">
                                        <h6 class="mb-0 text-primary">Anexos do Pedido</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="comprovante" class="form-label">Comprovante de Pagamento</label>
                                            <input type="file" class="form-control" id="comprovante" name="comprovante" accept=".pdf,.jpg,.png,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, JPG, PNG, ZIP, RAR (max 2MB)</small>
                                            @if($pedido->comprovante_path)
                                            <div class="mt-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-file me-1"></i> 
                                                    <a href="{{ route('pedidos.download', ['pedido' => $pedido->id, 'tipo' => 'comprovante']) }}" class="text-white">
                                                        {{ $pedido->comprovante_original_name }}
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-danger ms-2" 
                                                            onclick="if(confirm('Remover comprovante?')) window.location.href='{{ route('pedidos.remover-arquivo', ['pedido' => $pedido->id, 'tipo' => 'comprovante']) }}'">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="contrato" class="form-label">Contrato</label>
                                            <input type="file" class="form-control" id="contrato" name="contrato" accept=".pdf,.doc,.docx,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, DOC, DOCX, ZIP, RAR (max 2MB)</small>
                                            @if($pedido->contrato_path)
                                            <div class="mt-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-file me-1"></i> 
                                                    <a href="{{ route('pedidos.download', ['pedido' => $pedido->id, 'tipo' => 'contrato']) }}" class="text-white">
                                                        {{ $pedido->contrato_original_name }}
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-danger ms-2" 
                                                            onclick="if(confirm('Remover contrato?')) window.location.href='{{ route('pedidos.remover-arquivo', ['pedido' => $pedido->id, 'tipo' => 'contrato']) }}'">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="outros_arquivos" class="form-label">Outros Arquivos</label>
                                            <input type="file" class="form-control" id="outros_arquivos" name="outros_arquivos" accept=".pdf,.jpg,.png,.doc,.docx,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, JPG, PNG, DOC, DOCX, ZIP, RAR (max 5MB)</small>
                                            @if($pedido->outros_arquivos_path)
                                            <div class="mt-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-file me-1"></i> 
                                                    <a href="{{ route('pedidos.download', ['pedido' => $pedido->id, 'tipo' => 'outros_arquivos']) }}" class="text-white">
                                                        {{ $pedido->outros_arquivos_original_name }}
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-danger ms-2" 
                                                            onclick="if(confirm('Remover arquivo?')) window.location.href='{{ route('pedidos.remover-arquivo', ['pedido' => $pedido->id, 'tipo' => 'outros_arquivos']) }}'">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Seção de Produtos -->
                            <div class="col-md-6">
                                <div class="card mb-4 border-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-primary">Produtos do Pedido</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="adicionarProduto">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    </div>
                                    <div class="card-body">
                                    <div id="produtos-container">
                                    @foreach($pedido->produtos as $index => $produto)
                                        <div class="produto-item mb-3 p-3 border rounded">
                                            <input type="hidden" name="produtos[{{ $index }}][pivot_id]" value="{{ $produto->pivot->id }}">
                                            
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label class="form-label">Produto <span class="text-danger">*</span></label>
                                                    <select class="form-select produto-select" name="produtos[{{ $index }}][produto_id]" required>
                                                        <option value="">Selecione um produto</option>
                                                        @foreach($produtos as $prod)
                                                        <option value="{{ $prod->id }}" 
                                                            data-preco="{{ $prod->preco }}"
                                                            {{ $produto->id == $prod->id ? 'selected' : '' }}>
                                                            {{ $prod->nome }} (R$ {{ number_format($prod->preco, 2, ',', '.') }})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Qtd <span class="text-danger">*</span></label>
                                                    <input type="number" min="1" class="form-control produto-qtd" 
                                                        name="produtos[{{ $index }}][quantidade]" 
                                                        value="{{ $produto->pivot->quantidade }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Preço Unit. <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" min="0" class="form-control produto-preco" 
                                                        name="produtos[{{ $index }}][preco_unitario]" 
                                                        value="{{ $produto->pivot->preco_unitario }}" required>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-sm btn-danger remover-produto">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-8">
                                                    <label class="form-label">Observações</label>
                                                    <input type="text" class="form-control" 
                                                        name="produtos[{{ $index }}][observacoes]" 
                                                        value="{{ $produto->pivot->observacoes ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded">
                                            <div>
                                                <strong>Subtotal:</strong>
                                                <span id="subtotal">R$ {{ number_format($pedido->valor_total + $pedido->desconto, 2, ',', '.') }}</span>
                                            </div>
                                            <div>
                                                <strong>Desconto:</strong>
                                                <span id="desconto-display">R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</span>
                                            </div>
                                            <div class="h5 mb-0">
                                                <strong>Total:</strong>
                                                <span id="valor-total" class="text-success">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Atualizar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2 para o cliente
    $('#cliente_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione um cliente',
        allowClear: true
    });

    // Inicializar Select2 para produtos existentes
    $('.produto-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione um produto',
        allowClear: true
    });

    const produtosContainer = $('#produtos-container');
    const adicionarProdutoBtn = $('#adicionarProduto');
    
    // Adicionar novo produto
    adicionarProdutoBtn.on('click', function() {
        const novoIndex = Date.now();
        const produtoItem = $(`
            <div class="produto-item mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Produto <span class="text-danger">*</span></label>
                        <select class="form-select produto-select" name="produtos[${novoIndex}][produto_id]" required>
                            <option value="">Selecione um produto</option>
                            @foreach($produtos as $prod)
                            <option value="{{ $prod->id }}" data-preco="{{ $prod->preco }}">
                                {{ $prod->nome }} (R$ {{ number_format($prod->preco, 2, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qtd <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control produto-qtd" 
                               name="produtos[${novoIndex}][quantidade]" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Preço Unit. <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control produto-preco" 
                               name="produtos[${novoIndex}][preco_unitario]" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger remover-produto">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <label class="form-label">Observações</label>
                        <input type="text" class="form-control" 
                               name="produtos[${novoIndex}][observacoes]">
                    </div>
                </div>
            </div>
        `);
        
        produtosContainer.append(produtoItem);
        
        // Inicializar Select2 para o novo produto
        produtoItem.find('.produto-select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione um produto',
            allowClear: true
        }).on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const preco = selectedOption.data('preco') || 0;
            $(this).closest('.produto-item').find('.produto-preco').val(preco).trigger('change');
        });
        
        // Configurar eventos para o novo produto
        produtoItem.find('.produto-qtd, .produto-preco').on('change', atualizarValores);
        
        // Disparar evento change para calcular valores iniciais
        produtoItem.find('.produto-qtd').trigger('change');
    });
    
    // Remover produto
    produtosContainer.on('click', '.remover-produto', function() {
        const produtoItem = $(this).closest('.produto-item');
        
        // Adiciona um campo hidden para marcar o produto para remoção
        if (produtoItem.find('input[name$="[pivot_id]"]').length) {
            const pivotId = produtoItem.find('input[name$="[pivot_id]"]').val();
            produtoItem.append(`<input type="hidden" name="produtos_removidos[]" value="${pivotId}">`);
            produtoItem.hide(); // Oculta sem remover
        } else {
            produtoItem.remove(); // Remove completamente se for um novo produto
        }
        
        atualizarValores();
    });
    
    // Atualizar valores quando produtos ou quantidades mudam
    produtosContainer.on('change', '.produto-qtd, .produto-preco', atualizarValores);
    
    // Atualizar desconto
    $('#desconto').on('input', atualizarValores);
    
    // Calcular valores totais
    function atualizarValores() {
        let subtotal = 0;
        let descontoItens = 0;
        
        $('.produto-item:visible').each(function() {
            const qtd = parseFloat($(this).find('.produto-qtd').val()) || 0;
            const preco = parseFloat($(this).find('.produto-preco').val()) || 0;
            
            subtotal += qtd * preco;
        });
        
        const descontoGeral = parseFloat($('#desconto').val()) || 0;
        const total = Math.max(0, subtotal - descontoGeral);
        
        // Atualizar exibição
        $('#subtotal').text('R$ ' + subtotal.toFixed(2).replace('.', ','));
        $('#desconto-display').text('R$ ' + descontoGeral.toFixed(2).replace('.', ','));
        $('#valor-total').text('R$ ' + total.toFixed(2).replace('.', ','));
        
        // Atualizar campo oculto para envio ao servidor
        $('#valor_total').val(total.toFixed(2));
    }
    
    // Configurar evento change para produtos existentes
    $('.produto-select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const preco = selectedOption.data('preco') || 0;
        $(this).closest('.produto-item').find('.produto-preco').val(preco).trigger('change');
    });
    
    // Disparar change para produtos existentes para preencher preços
    $('.produto-select').trigger('change');

    // Configurar cálculo de data de entrega
    $('#dias_entrega').on('change', function() {
        if (this.value) {
            const dias = parseInt(this.value);
            if (dias > 0) {
                const dataPedido = new Date($('#data_pedido').val());
                if (isNaN(dataPedido.getTime())) {
                    return; // Data inválida
                }
                
                dataPedido.setDate(dataPedido.getDate() + dias);
                
                // Formata para o formato do input datetime-local
                const dataFormatada = dataPedido.toISOString().slice(0, 16);
                $('#data_entrega_prevista').val(dataFormatada);
            }
        }
    });
    
    // Se já houver um valor em dias_entrega, calcula ao carregar a página
    if ($('#dias_entrega').val()) {
        $('#dias_entrega').trigger('change');
    }
});
</script>
@endpush