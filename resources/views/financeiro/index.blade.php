@extends('layouts.app')

@section('title', 'Financeiro')
@section('content')
<div class="container-fluid">
    <!-- Header Modernizado -->
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-1 text-gray-800">Dashboard Financeiro</h1>
            <p class="mb-0 text-muted">Visão geral das finanças e desempenho dos pedidos</p>
        </div>
        <div class="d-flex">
            <div class="dropdown me-2">
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Últimos 6 meses</a></li>
                    <li><a class="dropdown-item" href="#">Este ano</a></li>
                    <li><a class="dropdown-item" href="#">Ano passado</a></li>
                </ul>
            </div>
            <a href="{{ route('financeiro.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text">Novo Lançamento</span>
            </a>
        </div>
    </div>

    <!-- Cards de Resumo Modernizados -->
    <div class="row">
        <!-- Saldo Atual -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-primary border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-primary mb-1">Saldo Atual</div>
                            <div class="h4 fw-bold">R$ {{ number_format($saldo, 2, ',', '.') }}</div>
                            <div class="small text-muted">Disponível</div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receitas -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-success border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-success mb-1">Total Receitas</div>
                            <div class="h4 fw-bold">R$ {{ number_format($totalReceitas + $receitaPedidosAnual, 2, ',', '.') }}</div>
                            <div class="mt-2 small">
                                <span class="badge bg-success-soft text-success">Lanç: R$ {{ number_format($totalReceitas, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Despesas -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-danger border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-danger mb-1">Total Despesas</div>
                            <div class="h4 fw-bold">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</div>
                            <div class="mt-2 small text-danger">
                                <i class="fas fa-exclamation-circle"></i> 
                                {{ $totalDespesas > 0 ? 
                                    number_format($totalDespesas / (($totalReceitas + $receitaPedidosAnual) ?: 1) * 100, 2) . '%' 
                                    : '0%' 
                                }}
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lucro Real -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-info border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-info mb-1">Lucro Real</div>
                            <div class="h4 fw-bold">R$ {{ number_format($lucroReal, 2, ',', '.') }}</div>
                            <div class="mt-2">
                                @if($lucroReal > 0)
                                    <span class="badge bg-success-soft text-success">
                                        <i class="fas fa-arrow-up"></i> Positivo
                                    </span>
                                @elseif($lucroReal < 0)
                                    <span class="badge bg-danger-soft text-danger">
                                        <i class="fas fa-arrow-down"></i> Negativo
                                    </span>
                                @else
                                    <span class="badge bg-warning-soft text-warning">
                                        <i class="fas fa-equals"></i> Neutro
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-warning border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-warning mb-1">Pedidos ({{ now()->year }})</div>
                            <div class="h4 fw-bold">R$ {{ number_format($receitaPedidosAnual, 2, ',', '.') }}</div>
                            <div class="mt-2 small">
                                <span class="badge bg-info-soft text-info">Lucro: R$ {{ number_format($lucroPedidos, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retiradas -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-secondary border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-secondary mb-1">Retiradas</div>
                            <div class="h4 fw-bold">R$ {{ number_format($totalRetirado, 2, ',', '.') }}</div>
                            <div class="mt-2 small text-muted">
                                {{ $totalRetirado > 0 ? number_format(($totalRetirado / ($totalReceitas + $receitaPedidosAnual) * 100), 2) . '%' : '0%' }}
                            </div>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Principais -->
    <div class="row">
        <!-- Gráfico de Receita vs Despesas -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Receita vs Despesas ({{ now()->year }})</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active">Anual</button>
                        <button type="button" class="btn btn-outline-secondary">Mensal</button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="receitaDespesaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Margem de Lucro por Mês -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold">Margem de Lucro (%)</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="margemLucroChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Secundários -->
    <div class="row">
        <!-- Top Produtos (Lucratividade) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold">Top 5 Produtos (Lucro)</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="topProdutosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status dos Pedidos -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold">Status dos Pedidos</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="statusPedidosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagamento dos Pedidos -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold">Pagamento dos Pedidos</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="pagamentoPedidosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Lançamentos Modernizada -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Últimos Lançamentos</h6>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Pesquisar...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="">
                                <tr>
                                    <th class="ps-3">Data</th>
                                    <th>Descrição</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-end pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lancamentos as $lancamento)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold">{{ $lancamento->data->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $lancamento->data->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $lancamento->descricao }}</div>
                                        <div class="small text-muted">{{ ucfirst($lancamento->observacoes) }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-{{ $lancamento->tipo === 'receita' ? 'success' : 'danger' }}">
                                        R$ {{ number_format($lancamento->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $lancamento->tipo === 'receita' ? 'success' : 'danger' }}-soft text-{{ $lancamento->tipo === 'receita' ? 'success' : 'danger' }}">
                                            {{ ucfirst($lancamento->tipo) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('financeiro.edit', $lancamento->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('financeiro.destroy', $lancamento->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este lançamento?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">
                        {{ $lancamentos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .progress {
        border-radius: 0.25rem;
    }
    .chart-tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        z-index: 100;
        display: none;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    // Gráfico de Receita vs Despesas
    const receitaDespesaCtx = document.getElementById('receitaDespesaChart').getContext('2d');
    const receitaDespesaChart = new Chart(receitaDespesaCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [
                {
                    label: 'Receitas',
                    data: {!! json_encode(array_values($receitaPedidosPorMes)) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Despesas',
                    data: {!! json_encode(array_values($despesasPorMes)) !!},
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.raw.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
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

    // Gráfico de Margem de Lucro
    const margemLucroCtx = document.getElementById('margemLucroChart').getContext('2d');
    const margemLucroChart = new Chart(margemLucroCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Margem de Lucro (%)',
                data: {!! json_encode(array_values($margemLucroPorMes)) !!},
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
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

    // Gráfico de Top Produtos (Lucratividade)
    const topProdutosCtx = document.getElementById('topProdutosChart').getContext('2d');
    const topProdutosChart = new Chart(topProdutosCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProdutosNomes) !!},
            datasets: [{
                label: 'Lucro (R$)',
                data: {!! json_encode($topProdutosLucros) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Lucro: R$ ' + context.raw.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Gráfico de Status dos Pedidos
    const statusPedidosCtx = document.getElementById('statusPedidosChart').getContext('2d');
    const statusPedidosChart = new Chart(statusPedidosCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusPedidosLabels) !!},
            datasets: [{
                data: {!! json_encode($statusPedidosValues) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Gráfico de Pagamento dos Pedidos
    const pagamentoPedidosCtx = document.getElementById('pagamentoPedidosChart').getContext('2d');
    const pagamentoPedidosChart = new Chart(pagamentoPedidosCtx, {
        type: 'pie',
        data: {
            labels: ['Pagos', 'Não Pagos'],
            datasets: [{
                data: {!! json_encode([$pedidosPagos, $pedidosNaoPagos]) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection