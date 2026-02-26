@extends('layouts.app')

@section('title', 'Detalhes do Cliente')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('clientes.index') }}">Clientes</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Detalhes</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Detalhes do Cliente</h5>
        <div class="btn-group">
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Informações Pessoais</h6>
                    <div class="ps-3">
                        <p><strong>Nome:</strong> {{ $cliente->nome }}</p>
                        <p><strong>E-mail:</strong> {{ $cliente->email }}</p>
                        <p><strong>Telefone:</strong> 
                            @if($cliente->telefone)
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $cliente->telefone) }}" 
                               target="_blank" class="text-decoration-none">
                                {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $cliente->telefone) }}
                                <i class="fab fa-whatsapp ms-2 text-success"></i>
                            </a>
                            @else
                            --
                            @endif
                        </p>
                        <p><strong>Documento:</strong> 
                            @if($cliente->cnpj_cpf)
                                @if(strlen(preg_replace('/[^0-9]/', '', $cliente->cnpj_cpf)) === 11)
                                    {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cliente->cnpj_cpf) }} (CPF)
                                @else
                                    {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cliente->cnpj_cpf) }} (CNPJ)
                                @endif
                            @else
                            --
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Informações Adicionais</h6>
                    <div class="ps-3">
                        <p><strong>Endereço:</strong> {{ $cliente->endereco ?? '--' }}</p>
                        <p><strong>Total de Pedidos:</strong> 
                            {{ $totalPedidos }}
                            @if($totalPedidos > 0)
                                <a href="{{ route('pedidos.index', ['cliente_id' => $cliente->id]) }}" 
                                class="btn btn-sm btn-outline-primary ms-2">
                                    Ver Pedidos
                                </a>
                            @endif
                        </p>
                        <p><strong>Cadastrado em:</strong> {{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última atualização:</strong> {{ $cliente->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        @if($cliente->observacoes)
        <div class="border-top pt-3 mt-3">
            <h6 class="text-muted mb-3">Observações</h6>
            <div class="bg-light p-3 rounded">
                {!! nl2br(e($cliente->observacoes)) !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection