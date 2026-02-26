@extends('layouts.app')

@section('title', 'Pedido #' . $pedido->id)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary">Pedido #{{ $pedido->id }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Informações do Pedido -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informações do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Cliente</h6>
                                <p class="mb-0 fw-bold">{{ $pedido->cliente->nome }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Data Criação</h6>
                                <p class="mb-0 fw-bold">{{ $pedido->data_pedido->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Dias para Entrega</h6>
                                <p class="mb-0 fw-bold">{{ $pedido->dias_entrega ?? 'Não definido' }} dias</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Entrega Prevista</h6>
                                <p class="mb-0 fw-bold">{{ $pedido->data_entrega_prevista ? $pedido->data_entrega_prevista->format('d/m/Y') : 'Não definida' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Status</h6>
                                <span class="badge bg-{{ 
                                    $pedido->status == 'orcamento' ? 'info' : 
                                    ($pedido->status == 'aberto' ? 'primary' : 
                                    ($pedido->status == 'em_producao' ? 'warning' : 
                                    ($pedido->status == 'finalizado' ? 'success' : 
                                    'dark'))) 
                                }} text-uppercase">
                                    {{ str_replace('_', ' ', $pedido->status) }}
                                </span>
                            </div>
                        </div>
                        
                        @php
                            $custoTotal = 0;
                            $custoMateriais = 0;
                            $custoMaoDeObra = 0;
                            $totalHoras = 0;
                            $totalMinutos = 0;
                            
                            foreach($pedido->produtos as $produto) {
                                // Custo de materiais
                                $custoMateriais += ($produto->preco_custo ?? 0) * $produto->pivot->quantidade;
                                
                                // Cálculo de horas totais
                                if($produto->tempo_impressao) {
                                    $time = explode(':', $produto->tempo_impressao);
                                    $horas = (int)$time[0];
                                    $minutos = (int)$time[1];
                                    $totalHoras += $horas * $produto->pivot->quantidade;
                                    $totalMinutos += $minutos * $produto->pivot->quantidade;
                                    
                                    // Converte minutos extras para horas
                                    $totalHoras += floor($totalMinutos / 60);
                                    $totalMinutos = $totalMinutos % 60;
                                    
                                    // Custo de mão de obra
                                    $horasTotaisProduto = $horas + ($minutos / 60);
                                    $custoMaoDeObra += $horasTotaisProduto * ($produto->custo_estimado_por_hora ?? 0) * $produto->pivot->quantidade;
                                }
                            }
                            
                            $custoTotal = $custoMateriais + $custoMaoDeObra;
                            $lucroEstimado = $pedido->valor_total - $custoTotal;
                            $margemLucro = $pedido->valor_total > 0 ? ($lucroEstimado / $pedido->valor_total) * 100 : 0;
                        @endphp
                        
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Horas Totais</h6>
                                <p class="mb-0 fw-bold">
                                    {{ $totalHoras }}h {{ $totalMinutos }}m
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Custo Total</h6>
                                <p class="mb-0 fw-bold">R$ {{ number_format($custoTotal, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Lucro Estimado</h6>
                                <p class="mb-0 fw-bold {{ $lucroEstimado >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($lucroEstimado, 2, ',', '.') }} ({{ number_format($margemLucro, 2) }}%)
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Valor Produtos</h6>
                                <p class="mb-0 fw-bold">R$ {{ number_format($pedido->valor_total + $pedido->desconto, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Desconto</h6>
                                <p class="mb-0 fw-bold">R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Status Pagamento</h6>
                                @if($pedido->pago)
                                    <span class="badge bg-success">Pago</span>
                                @else
                                    <span class="badge bg-danger">Pendente</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Valor Total</h6>
                                <p class="mb-0 fs-5 fw-bold text-success">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded">
                                <h6 class="text-muted small mb-1">Observações</h6>
                                <p class="mb-0">{{ $pedido->observacoes ?: 'Nenhuma observação' }}</p>
                            </div>
                        </div>
                        
                        <!-- Detalhes dos custos -->
                        <div class="col-12">
                            <div class="accordion" id="accordionCustos">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header" id="headingCustos">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustos" aria-expanded="false" aria-controls="collapseCustos">
                                            <i class="fas fa-calculator me-2"></i> Detalhes dos Custos
                                        </button>
                                    </h2>
                                    <div id="collapseCustos" class="accordion-collapse collapse" aria-labelledby="headingCustos" data-bs-parent="#accordionCustos">
                                        <div class="accordion-body p-0 pt-2">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Horas Totais de Impressão</td>
                                                    <td class="text-end">{{ $totalHoras }}h {{ $totalMinutos }}m</td>
                                                </tr>
                                                <tr>
                                                    <td>Custo de Materiais</td>
                                                    <td class="text-end">R$ {{ number_format($custoMateriais, 2, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Custo de Mão de Obra</td>
                                                    <td class="text-end">R$ {{ number_format($custoMaoDeObra, 2, ',', '.') }}</td>
                                                </tr>
                                                <tr class="table-active">
                                                    <td class="fw-bold">Custo Total</td>
                                                    <td class="text-end fw-bold">R$ {{ number_format($custoTotal, 2, ',', '.') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restante do código permanece igual -->
        <!-- Anexos -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-paperclip me-2 text-primary"></i>Anexos</h5>
                </div>
                <div class="card-body">
                    <!-- Comprovante -->
                    @if($pedido->comprovante_path)
                    <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="fas fa-file-invoice me-2 text-primary"></i>
                            <strong>Comprovante:</strong>
                            <a href="{{ route('pedidos.download', [$pedido->id, 'comprovante']) }}" target="_blank" class="text-decoration-none ms-2">
                                {{ $pedido->comprovante_original_name }}
                            </a>
                            <small class="text-muted d-block">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <a href="{{ route('pedidos.remover-arquivo', [$pedido->id, 'comprovante']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja remover este arquivo?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    @endif

                    <!-- Contrato -->
                    @if($pedido->contrato_path)
                    <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="fas fa-file-contract me-2 text-primary"></i>
                            <strong>Contrato:</strong>
                            <a href="{{ route('pedidos.download', [$pedido->id, 'contrato']) }}" target="_blank" class="text-decoration-none ms-2">
                                {{ $pedido->contrato_original_name }}
                            </a>
                            <small class="text-muted d-block">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <a href="{{ route('pedidos.remover-arquivo', [$pedido->id, 'contrato']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja remover este arquivo?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    @endif

                    <!-- Outros Arquivos -->
                    @if($pedido->outros_arquivos_path)
                    <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="fas fa-file-archive me-2 text-primary"></i>
                            <strong>Outros Arquivos:</strong>
                            <a href="{{ route('pedidos.download', [$pedido->id, 'outros_arquivos']) }}" target="_blank" class="text-decoration-none ms-2">
                                {{ $pedido->outros_arquivos_original_name }}
                            </a>
                            <small class="text-muted d-block">{{ $pedido->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <a href="{{ route('pedidos.remover-arquivo', [$pedido->id, 'outros_arquivos']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja remover este arquivo?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    @endif
                    
                    @if(!$pedido->comprovante_path && !$pedido->contrato_path && !$pedido->outros_arquivos_path)
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum arquivo anexado</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Restante do código permanece igual -->
    <!-- Ações -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-bolt me-2 text-primary"></i>Ações</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('orcamentos.gerarPdf', $pedido->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-pdf me-2"></i> Gerar Orçamento
                    </a>
                    <a href="{{ route('pedidos.gerarPdf', $pedido->id) }}" class="btn btn-outline-success">
                        <i class="fas fa-file-invoice me-2"></i> Gerar Pedido
                    </a>
                    <a href="{{ route('pedidos.gerarComprovanteEntrega', $pedido->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-file-signature me-2"></i> Comprovante Entrega
                    </a>
                </div>
                
                <div class="ms-auto">
                    @if(!$pedido->pago)
                    <form action="{{ route('pedidos.updateStatusPagamento', $pedido->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="pago" value="1">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-1"></i> Marcar como Pago
                        </button>
                    </form>
                    @else
                    <form action="{{ route('pedidos.updateStatusPagamento', $pedido->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="pago" value="0">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-times-circle me-1"></i> Marcar como Não Pago
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

        <!-- Atualizar Status -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-sync-alt me-2 text-primary"></i>Atualizar Status</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" class="row g-3 align-items-center">
                @csrf
                
                <div class="col-md-4">
                    <select class="form-select" name="status" id="statusSelect">
                        <option value="orcamento" {{ $pedido->status == 'orcamento' ? 'selected' : '' }}>Orçamento</option>
                        <option value="aberto" {{ $pedido->status == 'aberto' ? 'selected' : '' }}>Aberto</option>
                        <option value="em_producao" {{ $pedido->status == 'em_producao' ? 'selected' : '' }}>Em Produção</option>
                        <option value="finalizado" {{ $pedido->status == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="entregue" {{ $pedido->status == 'entregue' ? 'selected' : '' }}>Entregue</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Atualizar
                    </button>
                </div>
                <div class="col-md-6">
                    <div id="statusHelp" class="text-muted small">
                        @if($pedido->status == 'orcamento')
                        <i class="fas fa-info-circle me-1"></i> Este pedido está como orçamento. Mude para "Aberto" quando confirmado.
                        @elseif($pedido->status == 'aberto')
                        <i class="fas fa-info-circle me-1"></i> Pedido confirmado. Mude para "Em Produção" quando iniciar a fabricação.
                        @elseif($pedido->status == 'em_producao')
                        <i class="fas fa-info-circle me-1"></i> Em produção. Mude para "Finalizado" quando concluir.
                        @elseif($pedido->status == 'finalizado')
                        <i class="fas fa-info-circle me-1"></i> Produção concluída. Mude para "Entregue" após a entrega.
                        @else
                        <i class="fas fa-check-circle me-1 text-success"></i> Pedido entregue ao cliente.
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Produtos -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-boxes me-2 text-primary"></i>Produtos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produto</th>
                            <th>Qtd</th>
                            <th>Preço Unit.</th>
                            <th>Total</th>
                            <th>Tempo Impressão</th>
                            <th>Pronto</th>
                            <th>Observações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->produtos as $produto)
                        <tr class="{{ $produto->pivot->pronto ? 'table-success' : '' }}">
                            <td class="ps-4 fw-bold">{{ $produto->nome }}</td>
                            <td>{{ $produto->pivot->quantidade }}</td>
                            <td>R$ {{ number_format($produto->pivot->preco_unitario, 2, ',', '.') }}</td>
                            <td class="fw-bold">R$ {{ number_format($produto->pivot->preco_unitario * $produto->pivot->quantidade, 2, ',', '.') }}</td>
                            <td>
                                @if($produto->tempo_impressao)
                                    @php
                                        $time = explode(':', $produto->tempo_impressao);
                                        $hours = (int)$time[0];
                                        $minutes = (int)$time[1];
                                        $displayTime = '';
                                        if ($hours > 0) $displayTime .= $hours . 'h ';
                                        if ($minutes > 0) $displayTime .= $minutes . 'm';
                                    @endphp
                                    <span class="badge bg-info text-dark">
                                        {{ $displayTime ?: '0h' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('pedidos.updateProdutoStatus', [$pedido->id, $produto->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" role="switch" 
                                            onchange="this.form.submit()"
                                            name="pronto" value="1"
                                            {{ $produto->pivot->pronto ? 'checked' : '' }}>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <small>{{ $produto->pivot->observacoes ?: '-' }}</small>
                            </td>
                            <td>
                                <a href="{{ route('produtos.show', $produto->id) }}" 
                                class="btn btn-sm btn-outline-primary"
                                title="Ver detalhes do produto">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end ps-4">Subtotal</th>
                            <th class="fw-bold">R$ {{ number_format($pedido->valor_total + $pedido->desconto, 2, ',', '.') }}</th>
                            <th colspan="4"></th>
                        </tr>
                        @if($pedido->desconto > 0)
                        <tr>
                            <th colspan="3" class="text-end ps-4">Desconto</th>
                            <th class="fw-bold text-danger">- R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</th>
                            <th colspan="4"></th>
                        </tr>
                        @endif
                        <tr>
                            <th colspan="3" class="text-end ps-4">Total</th>
                            <th class="fw-bold text-success">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualiza o texto de ajuda quando o status muda
    document.getElementById('statusSelect').addEventListener('change', function() {
        const helpTexts = {
            'orcamento': 'Este pedido está como orçamento. Mude para "Aberto" quando confirmado.',
            'aberto': 'Pedido confirmado. Mude para "Em Produção" quando iniciar a fabricação.',
            'em_producao': 'Em produção. Mude para "Finalizado" quando concluir.',
            'finalizado': 'Produção concluída. Mude para "Entregue" após a entrega.',
            'entregue': 'Pedido entregue ao cliente.'
        };
        
        document.getElementById('statusHelp').innerHTML = 
            `<i class="fas fa-info-circle me-1"></i> ${helpTexts[this.value]}`;
    });
});
</script>
@endsection