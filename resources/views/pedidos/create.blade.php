@extends('layouts.app')

@section('title', 'Criar Novo Pedido')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
<li class="breadcrumb-item active" aria-current="page">Novo Pedido</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div>
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

                    <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data" id="pedidoForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Seção de Informações Básicas -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header titulo">
                                        <h6 class="mb-0">Informações do Pedido</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                                <option value="">Selecione um cliente</option>
                                                @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                                    {{ $cliente->nome }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="data_pedido" class="form-label">Data do Pedido</label>
                                            <input type="datetime-local" class="form-control" id="data_pedido" name="data_pedido" 
                                                   value="{{ old('data_pedido', now()->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="dias_entrega" class="form-label">Dias para entrega</label>
                                            <input type="number" class="form-control" id="dias_entrega" 
                                                name="dias_entrega" value="{{ old('dias_entrega') }}"
                                                min="1">
                                        </div>

                                        <div class="mb-3">
                                            <label for="data_entrega_prevista" class="form-label">Data de Entrega Prevista</label>
                                            <input type="datetime-local" class="form-control" id="data_entrega_prevista" 
                                                name="data_entrega_prevista" value="{{ old('data_entrega_prevista') }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="orcamento" {{ old('status') == 'orcamento' ? 'selected' : '' }}>Orçamento</option>
                                                <option value="aberto" {{ old('status', 'aberto') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                                <option value="em_producao" {{ old('status') == 'em_producao' ? 'selected' : '' }}>Em Produção</option>
                                                <option value="finalizado" {{ old('status') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                                <option value="entregue" {{ old('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 form-check form-switch">
                                            <input type="checkbox" name="pago" id="pago" 
                                                class="form-check-input" role="switch" 
                                                {{ old('pago', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pago">
                                                <i class="fas fa-check-circle me-1"></i> Pedido pago
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="desconto" class="form-label">Desconto (R$)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="desconto" 
                                                   name="desconto" value="{{ old('desconto', 0) }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="valor_total" class="form-label">Valor Total com Desconto</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="valor_total" 
                                                   name="valor_total" value="{{ old('valor_total', 0) }}" readonly>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">Observações</label>
                                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Seção de Arquivos -->
                                <div class="card mb-4">
                                    <div class="card-header titulo">
                                        <h6 class="mb-0">Anexos do Pedido</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="comprovante" class="form-label">Comprovante de Pagamento</label>
                                            <input type="file" class="form-control" id="comprovante" name="comprovante" accept=".pdf,.jpg,.png,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, JPG, PNG, ZIP, RAR (max 2MB)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="contrato" class="form-label">Contrato</label>
                                            <input type="file" class="form-control" id="contrato" name="contrato" accept=".pdf,.doc,.docx,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, DOC, DOCX, ZIP, RAR (max 2MB)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="outros_arquivos" class="form-label">Outros Arquivos</label>
                                            <input type="file" class="form-control" id="outros_arquivos" name="outros_arquivos" accept=".pdf,.jpg,.png,.doc,.docx,.zip,.rar">
                                            <small class="text-muted">Formatos: PDF, JPG, PNG, DOC, DOCX, ZIP, RAR (max 5MB)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Seção de Produtos -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header titulo d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Produtos do Pedido</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="adicionarProduto">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="produtos-container">
                                            @if(old('produtos'))
                                                @foreach(old('produtos') as $index => $produto)
                                                <div class="produto-item mb-3 p-3 border rounded titulo">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <label class="form-label">Produto <span class="text-danger">*</span></label>
                                                            <select class="form-select produto-select" name="produtos[{{ $index }}][produto_id]" required>
                                                                <option value="">Selecione um produto</option>
                                                                @foreach($produtos as $prod)
                                                                <option value="{{ $prod->id }}" 
                                                                    data-preco="{{ $prod->preco }}"
                                                                    {{ $produto['produto_id'] == $prod->id ? 'selected' : '' }}>
                                                                    {{ $prod->nome }} (R$ {{ number_format($prod->preco, 2, ',', '.') }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label">Qtd <span class="text-danger">*</span></label>
                                                            <input type="number" min="1" class="form-control produto-qtd" 
                                                                   name="produtos[{{ $index }}][quantidade]" 
                                                                   value="{{ $produto['quantidade'] }}" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Preço Unit. <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01" min="0" class="form-control produto-preco" 
                                                                   name="produtos[{{ $index }}][preco_unitario]" 
                                                                   value="{{ $produto['preco_unitario'] ?? $produtos->find($produto['produto_id'])->preco ?? 0 }}" required>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-sm btn-danger remover-produto">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Desconto (R$)</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" 
                                                                   name="produtos[{{ $index }}][desconto]" 
                                                                   value="{{ $produto['desconto'] ?? 0 }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label">Observações</label>
                                                            <input type="text" class="form-control" 
                                                                   name="produtos[{{ $index }}][observacoes]" 
                                                                   value="{{ $produto['observacoes'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <strong>Subtotal:</strong>
                                                <span id="subtotal">R$ 0,00</span>
                                            </div>
                                            <div>
                                                <strong>Desconto:</strong>
                                                <span id="desconto-display">R$ 0,00</span>
                                            </div>
                                            <div>
                                                <strong>Total:</strong>
                                                <span id="valor-total">R$ 0,00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar Pedido
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
document.addEventListener('DOMContentLoaded', function() {
    const produtosContainer = document.getElementById('produtos-container');
    const adicionarProdutoBtn = document.getElementById('adicionarProduto');
    const subtotalSpan = document.getElementById('subtotal');
    const descontoDisplay = document.getElementById('desconto-display');
    const valorTotalSpan = document.getElementById('valor-total');
    const descontoInput = document.getElementById('desconto');
    const valorTotalInput = document.getElementById('valor_total');
    let produtoIndex = {{ old('produtos') ? count(old('produtos')) : 0 }};
    
    // Função para atualizar preço quando produto é selecionado
    function atualizarPrecoProduto(selectElement) {
        const produtoItem = selectElement.closest('.produto-item');
        const precoInput = produtoItem.querySelector('.produto-preco');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.preco) {
            precoInput.value = parseFloat(selectedOption.dataset.preco).toFixed(2);
            atualizarValores();
        }
    }
    
    // Adicionar novo produto
    adicionarProdutoBtn.addEventListener('click', function() {
        const novoIndex = Date.now(); // Usa timestamp como índice único
        const produtoItem = document.createElement('div');
        produtoItem.className = 'produto-item mb-3 p-3 border rounded';
        produtoItem.innerHTML = `
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
                <div class="col-md-4">
                    <label class="form-label">Desconto (R$)</label>
                    <input type="number" step="0.01" min="0" class="form-control" 
                        name="produtos[${novoIndex}][desconto]" value="0">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Observações</label>
                    <input type="text" class="form-control" 
                        name="produtos[${novoIndex}][observacoes]">
                </div>
            </div>
        `;
        produtosContainer.appendChild(produtoItem);
        
        // Inicializar Select2 para o novo select
        $(produtoItem).find('.produto-select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Digite para buscar um produto',
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const preco = selectedOption.data('preco') || 0;
            $(this).closest('.produto-item').find('.produto-preco').val(preco).trigger('change');
        });
        
        // Configurar eventos para quantidade e preço
        $(produtoItem).find('.produto-qtd, .produto-preco, input[name$="[desconto]"]').on('change', function() {
            atualizarValores();
        });
        
        // Disparar evento change para calcular valores iniciais
        $(produtoItem).find('.produto-qtd').trigger('change');
    });
    
    // Remover produto
    produtosContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remover-produto') || 
            e.target.parentElement.classList.contains('remover-produto')) {
            const produtoItem = e.target.closest('.produto-item');
            produtoItem.remove();
            atualizarValores();
        }
    });
    
    // Calcular valores totais
    function atualizarValores() {
        let subtotal = 0;
        let descontoItens = 0;
        const produtos = document.querySelectorAll('.produto-item');
        
        produtos.forEach(produto => {
            const qtdInput = produto.querySelector('.produto-qtd');
            const precoInput = produto.querySelector('.produto-preco');
            const descontoInput = produto.querySelector('input[name$="[desconto]"]');
            
            if (qtdInput && qtdInput.value && precoInput && precoInput.value) {
                const quantidade = parseFloat(qtdInput.value);
                const preco = parseFloat(precoInput.value);
                const desconto = descontoInput ? parseFloat(descontoInput.value) || 0 : 0;
                
                subtotal += preco * quantidade;
                descontoItens += desconto;
            }
        });
        
        const descontoGeral = parseFloat(descontoInput.value) || 0;
        const total = Math.max(0, subtotal - descontoItens - descontoGeral);
        
        // Atualizar exibição
        subtotalSpan.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
        descontoDisplay.textContent = 'R$ ' + (descontoItens + descontoGeral).toFixed(2).replace('.', ',');
        valorTotalSpan.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        
        // Atualizar campo oculto para envio ao servidor
        valorTotalInput.value = total.toFixed(2);
    }
    
    // Configurar eventos para produtos pré-existentes
    document.querySelectorAll('.produto-select').forEach(select => {
        // Inicializar Select2
        $(select).select2({
            theme: 'bootstrap-5',
            placeholder: 'Digite para buscar um produto',
            allowClear: true,
            width: '100%'
        });
        
        // Configurar evento change
        select.addEventListener('change', function() {
            atualizarPrecoProduto(this);
        });
        
        // Definir preço inicial se já houver produto selecionado
        if (select.value) {
            const precoInput = select.closest('.produto-item').querySelector('.produto-preco');
            const selectedOption = select.options[select.selectedIndex];
            
            if (precoInput && !precoInput.value && selectedOption.dataset.preco) {
                const preco = parseFloat(selectedOption.dataset.preco);
                precoInput.value = preco.toFixed(2);
            }
        }
    });

    // Configurar eventos para quantidade e preço
    document.querySelectorAll('.produto-qtd, .produto-preco').forEach(input => {
        input.addEventListener('change', atualizarValores);
    });
    
    // Configurar cálculo de data de entrega
    const diasEntregaInput = document.getElementById('dias_entrega');
    const dataEntregaPrevistaInput = document.getElementById('data_entrega_prevista');
    const dataPedidoInput = document.getElementById('data_pedido');
    
    function calcularDataEntrega() {
        if (diasEntregaInput.value) {
            const dias = parseInt(diasEntregaInput.value);
            if (dias > 0) {
                const dataBase = dataPedidoInput.value ? new Date(dataPedidoInput.value) : new Date();
                const dataEntrega = new Date(dataBase);
                dataEntrega.setDate(dataEntrega.getDate() + dias);
                
                const dataFormatada = dataEntrega.toISOString().slice(0, 16);
                dataEntregaPrevistaInput.value = dataFormatada;
            }
        }
    }
    
    if (diasEntregaInput && dataEntregaPrevistaInput) {
        diasEntregaInput.addEventListener('change', calcularDataEntrega);
        diasEntregaInput.addEventListener('input', calcularDataEntrega);
        
        if (dataPedidoInput) {
            dataPedidoInput.addEventListener('change', calcularDataEntrega);
        }
    }
    
    // Inicializar valores se houver produtos
    if (produtoIndex > 0) {
        atualizarValores();
    }

    // Função para atualizar preço quando produto é selecionado
    function atualizarPrecoProduto(selectElement) {
        const produtoItem = selectElement.closest('.produto-item');
        const precoInput = produtoItem.querySelector('.produto-preco');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.preco) {
            const preco = parseFloat(selectedOption.dataset.preco);
            precoInput.value = preco.toFixed(2);
            atualizarValores();
        } else {
            precoInput.value = '0.00';
        }
    }
});
</script>
@endpush

@push('styles')
<style>
    .produto-item {
        transition: all 0.2s ease;
    }
/*     .produto-item:hover {
        background-color: #f8f9fa;
    } */
    .remover-produto {
        margin-bottom: 0.5rem;
    }
    #valor-total, #subtotal {
        font-size: 1.1rem;
        font-weight: bold;
    }
    #valor-total {
        color: #28a745;
    }
    #desconto-display {
        color: #dc3545;
    }
    .card-header.titulo {
        font-weight: 600;
    }
    .select2-container--bootstrap-5 .select2-selection {
        height: auto;
        min-height: 38px;
    }

    
    
</style>
@endpush