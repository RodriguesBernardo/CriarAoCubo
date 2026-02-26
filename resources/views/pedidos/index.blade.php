@extends('layouts.app')

@section('title', 'Gestão de Pedidos')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Pedidos</li>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Cabeçalho Simplificado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex gap-2">
            <!-- Dropdown Exportação -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('pedidos.export', ['type' => 'csv']) }}?search={{ $search }}&status={{ $status }}">
                        <i class="fas fa-file-csv me-2"></i> CSV
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('pedidos.export', ['type' => 'pdf']) }}?search={{ $search }}&status={{ $status }}">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </a></li>
                </ul>
            </div>
            
            <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Pedido
            </a>
        </div>
    </div>

    <!-- Cards de Status - Design Compacto -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <!-- Card Orçamentos -->
                        <div class="col-xl-3 col-md-6">
                            <div class="d-flex align-items-center p-3 rounded-3">
                                <div class="flex-shrink-0">
                                    <div class="p-2 rounded-circle bg-secondary bg-opacity-10">
                                        <i class="fas fa-file-invoice-dollar text-secondary fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-secondary">Orçamentos</h6>
                                    <h4 class="mb-0 fw-bold">{{ $orcamento->count() }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Card Pendentes -->
                        <div class="col-xl-3 col-md-6">
                            <div class="d-flex align-items-center p-3 rounded-3">
                                <div class="flex-shrink-0">
                                    <div class="p-2 rounded-circle bg-primary bg-opacity-10">
                                        <i class="fas fa-clock text-primary fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-primary">Pendentes</h6>
                                    <h4 class="mb-0 fw-bold">{{ $pendentes->count() }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Card Em Produção -->
                        <div class="col-xl-3 col-md-6">
                            <div class="d-flex align-items-center p-3 rounded-3 ">
                                <div class="flex-shrink-0">
                                    <div class="p-2 rounded-circle bg-info bg-opacity-10">
                                        <i class="fas fa-cogs text-info fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-info">Em Produção</h6>
                                    <h4 class="mb-0 fw-bold">{{ $em_producao->count() }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Card Finalizados -->
                        <div class="col-xl-3 col-md-6">
                            <div class="d-flex align-items-center p-3 rounded-3 ">
                                <div class="flex-shrink-0">
                                    <div class="p-2 rounded-circle bg-success bg-opacity-10">
                                        <i class="fas fa-check-circle text-success fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-success">Finalizados</h6>
                                    <h4 class="mb-0 fw-bold">{{ $finalizado->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificações de Pedidos Próximos da Entrega -->
    @if($pedidosProximos->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-warning d-flex align-items-center py-2">
                    <i class="fas fa-exclamation-triangle text-warning me-2 fs-6"></i>
                    <h6 class="mb-0 fw-bold text-warning">Pedidos Próximos da Data de Entrega</h6>
                    <span class="badge bg-warning text-dark ms-auto">{{ $pedidosProximos->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="">
                                <tr>
                                    <th class="border-0 ps-3">Nº Pedido</th>
                                    <th class="border-0">Cliente</th>
                                    <th class="border-0">Data Entrega</th>
                                    <th class="border-0">Dias Restantes</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidosProximos as $pedido)
                                <tr class="{{ \Carbon\Carbon::parse($pedido->data_entrega_prevista)->isToday() ? 'bg-danger bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                                    <td class="ps-3 fw-bold">#{{ $pedido->id }}</td>
                                    <td>{{ $pedido->cliente->nome }}</td>
                                    <td>{{ $pedido->data_entrega_prevista->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $diasRestantes = round(now()->diffInDays($pedido->data_entrega_prevista, false));
                                        @endphp
                                        @if($diasRestantes == 0)
                                            <span class="badge bg-danger">HOJE</span>
                                        @elseif($diasRestantes < 0)
                                            <span class="badge bg-danger">ATRASADO {{ abs($diasRestantes) }} DIAS</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $diasRestantes }} DIAS</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge text-dark bg-{{ 
                                            $pedido->status == 'em_producao' ? 'info' : 
                                            ($pedido->status == 'aberto' ? 'primary' : 'secondary') 
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                        </span>
                                    </td>
                                    <td class="pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Painéis de Status em Colunas -->
    <div class="row">
        <!-- Finalizados mas não pagos -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-danger bg-opacity-10 border-0 d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-danger flex-grow-1">Pendentes de Pagamento</h6>
                    <span class="badge bg-danger">{{ $finalizadosNaoPagos->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @forelse($finalizadosNaoPagos as $pedido)
                    <div class="card border-0 mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">#{{ $pedido->id }}</h6>
                                    <small class="text-muted">{{ $pedido->cliente->nome }}</small>
                                </div>
                                <span class="badge bg-danger">Finalizado</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>{{ $pedido->data_entrega_prevista->format('d/m/Y') }}
                                </small>
                                <span class="fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                                <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="status" value="entregue">
                                    @if($pedido->status == 'entregue')
                                        <button type="submit" class="btn btn-success btn-sm w-100" disabled>
                                            <i class="fas fa-truck"></i> Entregue
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                            <i class="fas fa-truck"></i> Entregue
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Nenhum pedido pendente de pagamento</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Orçamentos -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-secondary bg-opacity-10 border-0 d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice-dollar text-secondary me-2"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-secondary flex-grow-1">Orçamentos</h6>
                    <span class="badge bg-secondary">{{ $orcamento->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @forelse($orcamento as $pedido)
                    <div class="card border-0 mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">#{{ $pedido->id }}</h6>
                                    <small class="text-muted">{{ $pedido->cliente->nome }}</small>
                                </div>
                                <span class="badge bg-secondary">Orçamento</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>{{ $pedido->data_entrega_prevista->format('d/m/Y') }}
                                </small>
                                <span class="fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                                <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="status" value="aberto">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-check me-1"></i> Aprovar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Nenhum orçamento recente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pendentes -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary bg-opacity-10 border-0 d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-primary flex-grow-1">Pendentes</h6>
                    <span class="badge bg-primary">{{ $pendentes->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @forelse($pendentes as $pedido)
                    <div class="card border-0 mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">#{{ $pedido->id }}</h6>
                                    <small class="text-muted">{{ $pedido->cliente->nome }}</small>
                                </div>
                                <span class="badge bg-primary">Pendente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>{{ $pedido->data_entrega_prevista->format('d/m/Y') }}
                                </small>
                                <span class="fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                                <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="status" value="em_producao">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="fas fa-cogs me-1"></i> Produção
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clock fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Nenhum pedido pendente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Em Produção -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info bg-opacity-10 border-0 d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cogs text-info me-2"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-info flex-grow-1">Em Produção</h6>
                    <span class="badge bg-info">{{ $em_producao->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @forelse($em_producao as $pedido)
                    <div class="card border-0 mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">#{{ $pedido->id }}</h6>
                                    <small class="text-muted">{{ $pedido->cliente->nome }}</small>
                                </div>
                                <span class="badge bg-info">Produção</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>{{ $pedido->data_entrega_prevista->format('d/m/Y') }}
                                </small>
                                <span class="fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                                <form action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="status" value="finalizado">
                                    <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fas fa-check me-1"></i> Finalizar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-cogs fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Nenhum pedido em produção</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Pesquisa - Design Compacto -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted mb-1">Buscar pedidos</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Nº pedido, cliente..." value="{{ $search }}">
                        @if($search)
                        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-danger" type="button">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Filtrar por status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todos os status</option>
                        <option value="orcamento" {{ $status == 'orcamento' ? 'selected' : '' }}>Orçamentos</option>
                        <option value="aberto" {{ $status == 'aberto' ? 'selected' : '' }}>Pendentes</option>
                        <option value="em_producao" {{ $status == 'em_producao' ? 'selected' : '' }}>Em Produção</option>
                        <option value="finalizado" {{ $status == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                        <option value="entregue" {{ $status == 'entregue' ? 'selected' : '' }}>Entregues</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-sm w-100" type="submit">
                        <i class="fas fa-filter me-1"></i> Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista Completa de Pedidos -->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 py-3">
            <div class="d-flex align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Todos os Pedidos</h6>
                <span class="badge bg-primary ms-2">{{ $pedidos->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pedidosTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 ps-3" width="5%">Nº</th>
                            <th class="border-0" width="20%">Cliente</th>
                            <th class="border-0" width="10%">Data</th>
                            <th class="border-0" width="10%">Entrega</th>
                            <th class="border-0" width="10%">Status</th>
                            <th class="border-0" width="10%">Valor</th>
                            <th class="border-0" width="10%">Pago</th>
                            <th class="border-0" width="15%">Itens</th>
                            <th class="border-0 pe-3" width="10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                        <tr class="align-middle">
                            <td class="ps-3 fw-bold">#{{ $pedido->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                            <i class="fas fa-user text-primary fs-6"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="fw-medium">{{ $pedido->cliente->nome }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 150px;">
                                            {{ $pedido->observacoes ?: 'Sem observações' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($pedido->data_entrega_prevista)
                                    {{ \Carbon\Carbon::parse($pedido->data_entrega_prevista)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $pedido->status == 'orcamento' ? 'secondary' : 
                                    ($pedido->status == 'aberto' ? 'primary' : 
                                    ($pedido->status == 'em_producao' ? 'info' : 
                                    ($pedido->status == 'finalizado' ? 'success' : 'dark'))) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                </span>
                            </td>
                            <td class="fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                            <td>
                                @if($pedido->pago)
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Sim</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Não</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark border me-2">{{ $pedido->produtos->count() }} itens</span>
                                    <span class="text-muted small">{{ number_format($pedido->produtos->sum('pivot.quantidade'), 0, ',', '.') }} unid.</span>
                                </div>
                            </td>
                            <td class="pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-outline-primary" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-outline-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted">Nenhum pedido encontrado</h5>
                                    <p class="text-muted mb-0">Tente ajustar seus filtros de busca</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginação -->
            @if($pedidos->hasPages())
            <div class="card-footer border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted small">
                        Mostrando {{ $pedidos->firstItem() }} a {{ $pedidos->lastItem() }} de {{ $pedidos->total() }} resultados
                    </div>
                    <div>
                        {{ $pedidos->appends(['search' => $search, 'status' => $status])->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmação para exclusão
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if(!confirm('Tem certeza que deseja excluir este pedido? Esta ação não pode ser desfeita.')) {
                e.preventDefault();
            }
        });
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Notificações para pedidos urgentes
    const pedidosUrgentes = document.querySelectorAll('.bg-danger');
    if (pedidosUrgentes.length > 0) {
        console.warn(`ATENÇÃO: ${pedidosUrgentes.length} pedido(s) urgente(s) encontrado(s)!`);
    }
});
</script>
@endpush

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-secondary {
        border-left: 0.25rem solid #858796 !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .dropdown-menu {
        min-width: 180px;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border-radius: 0.35rem;
        margin: 0 2px;
    }
    
    /* Estados hover melhorados */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Cards de status compactos */
    .status-card {
        transition: all 0.2s ease;
    }
    
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    /* Responsividade melhorada */
    @media (max-width: 768px) {
        .card-body.p-2 .card {
            margin-bottom: 0.5rem;
        }
        
        .btn-group {
            flex-wrap: wrap;
        }
        
        .btn-group .btn {
            margin-bottom: 0.25rem;
        }
    }
    
    /* Animações suaves */
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

        .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-secondary {
        border-left: 0.25rem solid #858796 !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .dropdown-menu {
        min-width: 180px;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-secondary {
        border-left: 0.25rem solid #858796 !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .dropdown-menu {
        min-width: 180px;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }

    
    
</style>
@endpush