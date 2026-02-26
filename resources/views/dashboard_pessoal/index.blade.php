@extends('layouts.app')

@section('title', 'Dashboard Pessoal')

@section('content')
<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h6 class="text-uppercase text-secondary fw-bold mb-1" style="letter-spacing: 1px; font-size: 0.75rem;">Visão Geral</h6>
            <h2 class="fw-bold mb-0 ">{{ $saudacao }}, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
            <span class="text-muted">{{ \Carbon\Carbon::now()->isoFormat('dddd, D [de] MMMM') }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('calendario.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="far fa-calendar-alt me-2"></i>Agenda Completa
            </a>
            <a href="{{ route('financeiro_particular.index') }}" class="btn btn-primary rounded-pill px-4 text-white shadow-sm">
                <i class="fas fa-wallet me-2"></i>Financeiro
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4" style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); color: white;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25"><i class="far fa-clock fa-4x"></i></div>
                    <p class="mb-1 text-uppercase fw-bold opacity-75 small">Compromissos Hoje</p>
                    <h1 class="fw-bold mb-0 display-5">{{ $eventosHoje->count() }}</h1>
                    <small class="opacity-75">
                        @if($eventosHoje->count() > 0)
                            Próximo: {{ $eventosHoje->first()->inicio->format('H:i') }}
                        @else
                            Dia livre! Aproveite.
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-0 text-uppercase fw-bold text-secondary small">Saldo do Mês</p>
                            <h3 class="fw-bold mb-0 {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($saldo, 2, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-2 bg-success bg-opacity-10 rounded-circle text-success">
                            <i class="fas fa-dollar-sign fa-lg px-2"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Receita: R$ {{ number_format($receita, 2, ',', '.') }}</span>
                        <span>Gasto: {{ number_format($percentualGasto, 0) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar {{ $percentualGasto > 90 ? 'bg-danger' : ($percentualGasto > 70 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" style="width: {{ min($percentualGasto, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <p class="mb-0 text-uppercase fw-bold text-secondary small">A Pagar (7 dias)</p>
                        <div class="p-2 bg-danger bg-opacity-10 rounded-circle text-danger">
                            <i class="fas fa-file-invoice fa-lg px-2"></i>
                        </div>
                    </div>
                    
                    @if($contasProximas->count() > 0)
                        <div class="d-flex flex-column gap-2 mt-2">
                            @foreach($contasProximas as $conta)
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ $conta->data_vencimento->format('d/m') }}</div>
                                        <span class="small fw-semibold">{{ Str::limit($conta->descricao, 15) }}</span>
                                    </div>
                                    <span class="small fw-bold text-danger">R$ {{ number_format($conta->valor, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-2 text-success">
                            <i class="fas fa-check-circle mb-1"></i>
                            <p class="small mb-0 fw-bold">Tudo pago!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-0"><i class="far fa-calendar-check me-2 text-primary"></i>Sua Agenda</h5>
                </div>
                <div class="card-body p-4">
                    
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">Hoje</h6>
                    @forelse($eventosHoje as $evento)
                        <div class="d-flex gap-3 mb-3 align-items-start">
                            <div class="d-flex flex-column align-items-center" style="min-width: 50px;">
                                <span class="fw-bold">{{ $evento->inicio->format('H:i') }}</span>
                                <div class="h-100 border-start border-2 mt-1" style="border-color: {{ $evento->cor }} !important; min-height: 20px;"></div>
                            </div>
                            <div class="card border-0 shadow-sm flex-fill" style="background-color: #f8f9fa;">
                                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1" style="color: {{ $evento->cor }}">{{ $evento->titulo }}</h6>
                                        <div class="small text-muted">
                                            @if(!empty($evento->participantes) && is_array($evento->participantes))
                                                <i class="fas fa-users me-1"></i> {{ implode(', ', $evento->participantes) }}
                                            @endif
                                            @if($evento->descricao)
                                                <span class="mx-1">•</span> {{ Str::limit($evento->descricao, 30) }}
                                            @endif
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-light rounded-circle" onclick="window.location='{{ route('calendario.index') }}'">
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light border-0 d-flex align-items-center" role="alert">
                            <i class="fas fa-couch me-3 text-secondary fa-2x"></i>
                            <div>
                                <span class="fw-bold">Nada agendado para hoje.</span>
                                <p class="mb-0 small text-muted">Aproveite para organizar suas tarefas ou descansar.</p>
                            </div>
                        </div>
                    @endforelse

                    <h6 class="text-uppercase text-secondary fw-bold small mt-4 mb-3">Próximos Dias</h6>
                    <div class="list-group list-group-flush">
                        @forelse($proximosEventos as $evento)
                            <div class="list-group-item border-0 px-0 py-2 d-flex align-items-center">
                                <div class="badge rounded-3 me-3 py-2" style="background-color: {{ $evento->cor }}20; color: {{ $evento->cor }}; width: 50px; text-align: center;">
                                    <div class="fw-bold" style="font-size: 0.7rem;">{{ strtoupper($evento->inicio->isoFormat('MMM')) }}</div>
                                    <div class="fw-bold fs-5">{{ $evento->inicio->format('d') }}</div>
                                </div>
                                <div class="flex-fill">
                                    <h6 class="mb-0 fw-bold">{{ $evento->titulo }}</h6>
                                    <small class="text-muted">{{ $evento->inicio->isoFormat('dddd') }} às {{ $evento->inicio->format('H:i') }}</small>
                                </div>
                                @if(!empty($evento->participantes) && is_array($evento->participantes))
                                    <div class="avatar-group">
                                        @foreach($evento->participantes as $part)
                                            <span class="badge rounded-pill border text-secondary" style="font-size: 0.6rem">{{ substr($part, 0, 1) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sem eventos futuros próximos.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 "><i class="fas fa-chart-pie me-2 text-success"></i>Balanço do Mês</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center">
                    
                    <div style="width: 250px; height: 250px; position: relative;">
                        <canvas id="graficoResumo"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Saldo</small>
                            <span class="fw-bold fs-5 {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $saldo >= 0 ? '+' : '' }}{{ number_format($saldo, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="row w-100 mt-4 text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Receitas</small>
                            <h5 class="fw-bold text-success mb-0">R$ {{ number_format($receita, 2, ',', '.') }}</h5>
                        </div>
                        <div class="col-6">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Despesas</small>
                            <h5 class="fw-bold text-danger mb-0">R$ {{ number_format($despesa, 2, ',', '.') }}</h5>
                        </div>
                    </div>

                    <div class="mt-4 w-100">
                        <a href="{{ route('financeiro_particular.index') }}" class="btn btn-light w-100 rounded-pill fw-bold text-secondary">
                            Ver Extrato Detalhado <i class="fas fa-arrow-right ms-2 small"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoResumo');
    
    // Dados vindos do controller
    const receita = {{ $receita }};
    const despesa = {{ $despesa }};
    
    // Se ambos forem 0, mostra um gráfico cinza vazio
    const dados = (receita == 0 && despesa == 0) ? [1] : [receita, despesa];
    const cores = (receita == 0 && despesa == 0) ? ['#e5e7eb'] : ['#10b981', '#ef4444'];
    const labels = (receita == 0 && despesa == 0) ? ['Sem dados'] : ['Receitas', 'Despesas'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dados,
                backgroundColor: cores,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '75%', // Rosca fina
            plugins: {
                legend: { display: false },
                tooltip: { enabled: (receita != 0 || despesa != 0) }
            }
        }
    });
</script>
@endpush