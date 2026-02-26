@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('clientes.index') }}">Clientes</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('clientes.show', $cliente->id) }}">{{ $cliente->nome }}</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Editar Cliente</h5>
        <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" name="nome" value="{{ old('nome', $cliente->nome) }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $cliente->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone *</label>
                        <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                               id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone_formatado) }}" required>
                        @error('telefone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cnpj_cpf" class="form-label">CPF/CNPJ</label>
                        <input type="text" class="form-control @error('cnpj_cpf') is-invalid @enderror" 
                               id="cnpj_cpf" name="cnpj_cpf" value="{{ old('cnpj_cpf', $cliente->documento_formatado) }}">
                        @error('cnpj_cpf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                               id="endereco" name="endereco" value="{{ old('endereco', $cliente->endereco) }}">
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                          id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $cliente->observacoes) }}</textarea>
                @error('observacoes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Salvar Alterações
                </button>
                <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        // Máscara para telefone
        $('#telefone').mask('(00) 00000-0000');
        
        // Máscara dinâmica para CPF/CNPJ
        $('#cnpj_cpf').keydown(function(){
            try {
                var value = $(this).val().replace(/\D/g, '');
                if(value.length <= 11){
                    $(this).mask('000.000.000-00');
                } else {
                    $(this).mask('00.000.000/0000-00');
                }
            } catch(e) {}
        });
        
        // Aplicar máscara inicial
        var cpfCnpj = $('#cnpj_cpf').val().replace(/\D/g, '');
        if(cpfCnpj.length <= 11){
            $('#cnpj_cpf').mask('000.000.000-00');
        } else {
            $('#cnpj_cpf').mask('00.000.000/0000-00');
        }
    });
</script>
@endpush