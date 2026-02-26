@extends('layouts.app')

@section('title', 'Visão Geral')

@section('content')
<div class="container-fluid p-4">
    
    <div class="mb-5">
        <h2 class="fw-bold  mb-1">{{ $saudacao }}, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
        <p class="text-secondary mb-0">Aqui está o resumo do seu dia e dos seus negócios.</p>
    </div>

    <div class="d-flex gap-3 mb-5 overflow-auto pb-2">
        <a href="{{ route('pedidos.create') }}" class="btn btn-white border shadow-sm rounded-pill px-4 py-2 flex-shrink-0">
            <i class="fas fa-cube text-primary me-2"></i>Novo Pedido
        </a>
        <a href="{{ route('financeiro_particular.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4 py-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalLancamento">
            <i class="fas fa-wallet text-success me-2"></i>Gasto Pessoal
        </a>
        <a href="{{ route('calendario.index') }}" class="btn btn-white border shadow-sm rounded-pill px-4 py-2 flex-shrink-0">
            <i class="fas fa-calendar-plus text-info me-2"></i>Agendar
        </a>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-building small"></i>
                </div>
                <h5 class="fw-bold mb-0 text-primary">Empresa (Criar³)</h5>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem;">Faturamento (Mês)</small>
                            <h3 class="fw-bold  mb-0 mt-1">R$ {{ number_format($faturamentoEmpresa, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4 {{ $pedidosAtrasados > 0 ? 'bg-danger bg-opacity-10' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem;">Em Produção</small>
                                    <h3 class="fw-bold {{ $pedidosAtrasados > 0 ? 'text-danger' : 'text-primary' }} mb-0 mt-1">{{ $pedidosEmProducao }}</h3>
                                </div>
                                @if($pedidosAtrasados > 0)
                                    <div class="text-end">
                                        <span class="badge bg-danger rounded-pill">{{ $pedidosAtrasados }} Atrasados</span>
                                    </div>
                                @else
                                    <div class="text-end text-success opacity-50">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <a href="{{ route('dashboard') }}" class="d-flex justify-content-between align-items-center p-4 text-decoration-none  hover-bg-light transition">
                        <div>
                            <h6 class="fw-bold mb-1">Dashboard Empresarial Completo</h6>
                            <small class="text-muted">Ver métricas detalhadas, top produtos e clientes</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-user small"></i>
                </div>
                <h5 class="fw-bold mb-0 text-success">Pessoal & Agenda</h5>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem;">Saldo Líquido (Mês)</small>
                            <h3 class="fw-bold {{ $saldoPessoal >= 0 ? 'text-success' : 'text-danger' }} mb-0 mt-1">
                                R$ {{ number_format($saldoPessoal, 2, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 rounded-4 ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem;">Agenda Hoje</small>
                                    <h3 class="fw-bold  mb-0 mt-1">{{ $eventosHoje->count() }} <span class="fs-6 text-muted fw-normal">eventos</span></h3>
                                </div>
                                <i class="far fa-calendar text-primary opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header  border-0 pt-3 px-3">
                            <small class="fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">Contas a Pagar (7 dias)</small>
                        </div>
                        <div class="card-body p-0">
                            @forelse($contasProximas as $conta)
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-danger bg-opacity-10 text-danger">{{ $conta->data_vencimento->format('d/m') }}</div>
                                        <span class="small fw-semibold ">{{ $conta->descricao }}</span>
                                    </div>
                                    <span class="small fw-bold text-danger">R$ {{ number_format($conta->valor, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="text-center py-3 text-success">
                                    <i class="fas fa-check-circle mb-1"></i>
                                    <p class="small mb-0">Nenhuma conta próxima!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header  border-0 pt-3 px-3">
                            <small class="fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">Agenda de Hoje</small>
                        </div>
                        <div class="card-body p-0">
                            @forelse($eventosHoje as $evento)
                                <div class="d-flex align-items-center px-3 py-2 border-bottom border-light">
                                    <div class="me-3 text-center" style="min-width: 40px;">
                                        <span class="d-block fw-bold small ">{{ $evento->inicio->format('H:i') }}</span>
                                    </div>
                                    <div class="border-start ps-3" style="border-color: {{ $evento->cor }} !important; border-width: 3px !important;">
                                        <span class="d-block small fw-bold ">{{ $evento->titulo }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted opacity-50">
                                    <p class="small mb-0">Livre hoje.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover {
        /* background-color: #f8f9fa; */
        cursor: pointer;
    }
    .transition {
        transition: all 0.2s ease;
    }
</style>
@endsection