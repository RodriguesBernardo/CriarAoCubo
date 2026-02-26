@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Cards de Métricas Principais -->
    <div class="row mb-4">
        <!-- Receita Total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold small">Receita Total</h6>
                            <h2 class="mb-0 fw-bold">R$ {{ number_format($receitaMes, 2, ',', '.') }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-dollar-sign fa-lg text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $variacaoReceita >= 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $variacaoReceita >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $variacaoReceita >= 0 ? 'up' : 'down' }}"></i> {{ abs($variacaoReceita) }}% 
                        </span>
                        <span class="text-muted small">vs mês anterior</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Pedidos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold small">Total de Pedidos</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalPedidos }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-shopping-bag fa-lg text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-up"></i> {{ $variacaoPedidos }}% 
                        </span>
                        <span class="text-muted small">vs mês anterior</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Médio -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold small">Ticket Médio</h6>
                            <h2 class="mb-0 fw-bold">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-receipt fa-lg text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $variacaoTicket >= 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $variacaoTicket >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $variacaoTicket >= 0 ? 'up' : 'down' }}"></i> {{ abs($variacaoTicket) }}% 
                        </span>
                        <span class="text-muted small">vs mês anterior</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Margem de Lucro -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold small">Margem de Lucro</h6>
                            <h2 class="mb-0 fw-bold">{{ $margemLucro }}%</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-chart-line fa-lg text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $variacaoMargem >= 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $variacaoMargem >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $variacaoMargem >= 0 ? 'up' : 'down' }}"></i> {{ abs($variacaoMargem) }}% 
                        </span>
                        <span class="text-muted small">vs mês anterior</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Principais -->
    <div class="row mb-4">
        <!-- Desempenho Anual -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Desempenho Anual</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt me-1"></i> Ano
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Ano</a></li>
                            <li><a class="dropdown-item" href="#">6 Meses</a></li>
                            <li><a class="dropdown-item" href="#">3 Meses</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="annualPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribuição de Pedidos -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3">
                    <h6 class="m-0 fw-bold">Distribuição de Pedidos</h6>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 250px;">
                        <canvas id="ordersDistributionChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($statusPedidos as $status)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge-dot me-2 bg-{{
                                ['orcamento' => 'info',
                                 'aberto' => 'primary',
                                 'em_producao' => 'warning',
                                 'finalizado' => 'success',
                                 'entregue' => 'dark'][$status->status] 
                            }}"></span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</span>
                                    <span class="fw-bold small">{{ $status->total }} ({{ round(($status->total/$totalPedidos)*100) }}%)</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Secundárias -->
    <div class="row mb-4">
        <!-- Clientes Ativos -->
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-purple bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-users fa-lg text-purple"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ $totalClientes }}</h3>
                    <p class="text-muted small mb-0">Clientes Ativos</p>
                    <span class="badge bg-success bg-opacity-10 text-success small">
                        <i class="fas fa-arrow-up"></i> {{ $variacaoClientes }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Produtos Cadastrados -->
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-orange bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-cubes fa-lg text-orange"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ $totalProdutos }}</h3>
                    <p class="text-muted small mb-0">Produtos Cadastrados</p>
                    <span class="badge bg-success bg-opacity-10 text-success small">
                        <i class="fas fa-arrow-up"></i> {{ $variacaoProdutos }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Pedidos Atrasados -->
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-clock fa-lg text-danger"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ $pedidosAtrasados->count() }}</h3>
                    <p class="text-muted small mb-0">Pedidos Atrasados</p>
                    <a href="#late-orders" class="btn btn-sm btn-outline-danger mt-2">Ver Detalhes</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Análise de Receita e Custos -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 py-3">
                    <h6 class="m-0 fw-bold">Análise de Receita e Custos (Últimos 6 Meses)</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="revenueCostAnalysisChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados Detalhados -->
    <div class="row">
        <!-- Top Clientes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Top Clientes</h6>
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="">
                                <tr>
                                    <th class="ps-4">Cliente</th>
                                    <th class="text-end">Pedidos</th>
                                    <th class="text-end">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientesMaisCompraram as $cliente)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                                {{ substr($cliente->nome, 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('clientes.show', $cliente->id) }}" class="text-decoration-none fw-bold">{{ $cliente->nome }}</a>
                                                <p class="small text-muted mb-0">{{ $cliente->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end align-middle">{{ $cliente->pedidos_count }}</td>
                                    <td class="text-end align-middle fw-bold">R$ {{ number_format($cliente->valor_total, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos Mais Vendidos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Produtos Mais Vendidos</h6>
                    <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Produto</th>
                                    <th class="text-end">Vendas</th>
                                    <th class="text-end">Receita</th>
                                    <th class="text-end">Margem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtosMaisVendidos as $produto)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-info bg-opacity-10 text-info rounded-circle me-3">
                                                {{ substr($produto->nome, 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('produtos.show', $produto->id) }}" class="text-decoration-none fw-bold">{{ Str::limit($produto->nome, 25) }}</a>
                                                <p class="small text-muted mb-0">Cód: {{ $produto->codigo }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end align-middle">{{ $produto->vendas_count }}</td>
                                    <td class="text-end align-middle">R$ {{ number_format($produto->receita_total, 2, ',', '.') }}</td>
                                    <td class="text-end align-middle fw-bold {{ $produto->margem >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($produto->margem, 2) }}%
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

    <!-- Pedidos Recentes e Atrasados -->
    <div class="row">
        <!-- Pedidos Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Pedidos Recentes</h6>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nº</th>
                                    <th>Cliente</th>
                                    <th>Status</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosPedidos as $pedido)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        <a href="{{ route('pedidos.show', $pedido->id) }}" class="text-decoration-none">#{{ $pedido->id }}</a>
                                    </td>
                                    <td>{{ Str::limit($pedido->cliente->nome, 20) }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            ['orcamento' => 'info',
                                             'aberto' => 'primary',
                                             'em_producao' => 'warning',
                                             'finalizado' => 'success',
                                             'entregue' => 'dark'][$pedido->status] 
                                        }} bg-opacity-10 text-{{
                                            ['orcamento' => 'info',
                                             'aberto' => 'primary',
                                             'em_producao' => 'warning',
                                             'finalizado' => 'success',
                                             'entregue' => 'dark'][$pedido->status] 
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Atrasados -->
        <div class="col-lg-6 mb-4" id="late-orders">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Pedidos Atrasados</h6>
                    <span class="badge bg-danger">{{ $pedidosAtrasados->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nº</th>
                                    <th>Cliente</th>
                                    <th>Atraso</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidosAtrasados as $pedido)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        <a href="{{ route('pedidos.show', $pedido->id) }}" class="text-decoration-none">#{{ $pedido->id }}</a>
                                    </td>
                                    <td>{{ Str::limit($pedido->cliente->nome, 20) }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $pedido->dias_atraso }} dias</span>
                                    </td>
                                    <td class="text-end fw-bold">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                @if($pedidosAtrasados->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle text-success me-2"></i> Nenhum pedido atrasado
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        font-weight: 600;
    }
    .badge-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Gráfico de Desempenho Anual
    const annualPerformanceCtx = document.getElementById('annualPerformanceChart').getContext('2d');
    const annualPerformanceChart = new Chart(annualPerformanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Receita',
                    data: [
                        @foreach(range(1, 12) as $mes)
                            {{ $receitaPorMes->where('mes', $mes)->first()->total ?? 0 }},
                        @endforeach
                    ],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Pedidos',
                    data: [
                        @foreach(range(1, 12) as $mes)
                            {{ $pedidosPorMes->where('mes', $mes)->first()->total ?? 0 }},
                        @endforeach
                    ],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    borderWidth: 2,
                    tension: 0.3,
                    borderDash: [5, 5],
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 0) {
                                label += 'R$ ' + context.raw.toLocaleString('pt-BR');
                            } else {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    },
                    title: {
                        display: true,
                        text: 'Receita (R$)'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Nº de Pedidos'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gráfico de Distribuição de Pedidos
    const ordersDistributionCtx = document.getElementById('ordersDistributionChart').getContext('2d');
    const ordersDistributionChart = new Chart(ordersDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($statusPedidos as $status)
                    "{{ ucfirst(str_replace('_', ' ', $status->status)) }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($statusPedidos as $status)
                        {{ $status->total }},
                    @endforeach
                ],
                backgroundColor: [
                    '#36b9cc', // orcamento
                    '#4e73df', // aberto
                    '#f6c23e', // em_producao
                    '#1cc88a', // finalizado
                    '#5a5c69'  // entregue
                ],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.raw;
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Análise de Receita e Custos
    const revenueCostAnalysisCtx = document.getElementById('revenueCostAnalysisChart').getContext('2d');
    const revenueCostAnalysisChart = new Chart(revenueCostAnalysisCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($ultimos6Meses as $mes)
                    "{{ $mes['nome'] }}",
                @endforeach
            ],
            datasets: [
                {
                    label: 'Receita',
                    data: [
                        @foreach($ultimos6Meses as $mes)
                            {{ $mes['receita'] }},
                        @endforeach
                    ],
                    backgroundColor: '#4e73df',
                    borderRadius: 4
                },
                {
                    label: 'Custo',
                    data: [
                        @foreach($ultimos6Meses as $mes)
                            {{ $mes['custo'] }},
                        @endforeach
                    ],
                    backgroundColor: '#e74a3b',
                    borderRadius: 4
                },
                {
                    label: 'Lucro',
                    data: [
                        @foreach($ultimos6Meses as $mes)
                            {{ $mes['lucro'] }},
                        @endforeach
                    ],
                    backgroundColor: '#1cc88a',
                    borderRadius: 4
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.raw.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection