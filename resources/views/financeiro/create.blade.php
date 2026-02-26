@extends('layouts.app')

@section('title', 'Novo Lançamento Financeiro')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('financeiro.index') }}">Financeiro</a></li>
<li class="breadcrumb-item active" aria-current="page">Novo Lançamento</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Novo Lançamento Financeiro
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

                    <form action="{{ route('financeiro.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Informações do Lançamento</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="descricao" name="descricao" required
                                                   value="{{ old('descricao') }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" step="0.01" min="0.01" class="form-control" 
                                                       id="valor" name="valor" required value="{{ old('valor') }}">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="data" class="form-label">Data <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="data" name="data" required
                                                   value="{{ old('data', date('Y-m-d')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Classificação</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                            <select class="form-select" id="tipo" name="tipo" required>
                                                <option value="">Selecione...</option>
                                                <option value="receita" {{ old('tipo') == 'receita' ? 'selected' : '' }}>Receita</option>
                                                <option value="despesa" {{ old('tipo') == 'despesa' ? 'selected' : '' }}>Despesa</option>
                                                <option value="despesa" {{ old('tipo') == 'retirado' ? 'selected' : '' }}>Retirado</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="categoria" name="categoria" required
                                                   value="{{ old('categoria') }}" list="categorias">
                                            <datalist id="categorias">
                                                <option value="Venda de Produtos">
                                                <option value="Serviços">
                                                <option value="Matéria-Prima">
                                                <option value="Salários">
                                                <option value="Aluguel">
                                                <option value="Manutenção">
                                            </datalist>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">Observações</label>
                                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('financeiro.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar Lançamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush