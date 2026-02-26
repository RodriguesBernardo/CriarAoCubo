@extends('layouts.app')

@section('title', 'Novo Cliente')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('clientes.index') }}">Clientes</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Novo</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header py-3 titlos">
        <h5 class="mb-0 ">Cadastrar Novo Cliente</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" name="nome" value="{{ old('nome') }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone *</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                           id="telefone" name="telefone" value="{{ old('telefone') }}" 
                           placeholder="(00) 00000-0000" required>
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="cnpj_cpf" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control @error('cnpj_cpf') is-invalid @enderror" 
                           id="cnpj_cpf" name="cnpj_cpf" value="{{ old('cnpj_cpf') }}"
                           placeholder="000.000.000-00 ou 00.000.000/0000-00">
                    @error('cnpj_cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Nova seção para busca por CEP -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="cep" class="form-label">CEP</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                               id="cep" name="cep" value="{{ old('cep') }}"
                               placeholder="00000-000">
                        <button class="btn btn-outline-secondary" type="button" id="buscar-cep">
                            <i class="fas fa-search"></i>
                        </button>
                        @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="logradouro" class="form-label">Endereço</label>
                    <input type="text" class="form-control @error('logradouro') is-invalid @enderror" 
                           id="logradouro" name="logradouro" value="{{ old('logradouro') }}">
                    @error('logradouro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                           id="numero" name="numero" value="{{ old('numero') }}">
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
                           id="complemento" name="complemento" value="{{ old('complemento') }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
                           id="bairro" name="bairro" value="{{ old('bairro') }}">
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                           id="cidade" name="cidade" value="{{ old('cidade') }}">
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control @error('estado') is-invalid @enderror" 
                           id="estado" name="estado" value="{{ old('estado') }}">
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Campo endereço completo oculto que será preenchido automaticamente -->
            <input type="hidden" id="endereco" name="endereco" value="{{ old('endereco') }}">
            
            <div class="mb-4">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                          id="observacoes" name="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        // Máscara para telefone
        $('#telefone').mask('(00) 00000-0000');
        
        // Máscara para CEP
        $('#cep').mask('00000-000');
        
        // Máscara dinâmica para CPF/CNPJ
        $('#cnpj_cpf').keydown(function() {
            try {
                const length = $(this).val().replace(/\D/g, '').length;
                if (length <= 11) {
                    $(this).mask('000.000.000-009');
                } else {
                    $(this).mask('00.000.000/0000-00');
                }
            } catch (e) {}
        }).trigger('keydown');
        
        // Busca de CEP
        $('#buscar-cep').click(function() {
            const cep = $('#cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('CEP inválido. Digite um CEP com 8 dígitos.');
                return;
            }
            
            // Mostra loading no botão
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            
            // Consulta a API ViaCEP
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
                .done(function(data) {
                    if (data.erro) {
                        alert('CEP não encontrado.');
                        return;
                    }
                    
                    // Preenche os campos com os dados retornados
                    $('#logradouro').val(data.logradouro || '');
                    $('#bairro').val(data.bairro || '');
                    $('#cidade').val(data.localidade || '');
                    $('#estado').val(data.uf || '');
                    
                    // Preenche o campo endereço completo (oculto)
                    const enderecoCompleto = `${data.logradouro || ''}, ${$('#numero').val() || ''} ${$('#complemento').val() ? '-' + $('#complemento').val() : ''}, ${data.bairro || ''}, ${data.localidade || ''} - ${data.uf || ''}`;
                    $('#endereco').val(enderecoCompleto);
                    
                    // Foca no campo número após a busca
                    $('#numero').focus();
                })
                .fail(function() {
                    alert('Erro ao buscar CEP. Tente novamente.');
                })
                .always(function() {
                    // Restaura o ícone do botão
                    $('#buscar-cep').html('<i class="fas fa-search"></i>');
                });
        });
        
        // Atualiza o endereço completo quando os campos são alterados
        $('#numero, #complemento').on('change keyup', function() {
            const enderecoCompleto = `${$('#logradouro').val() || ''}, ${$('#numero').val() || ''} ${$('#complemento').val() ? '-' + $('#complemento').val() : ''}, ${$('#bairro').val() || ''}, ${$('#cidade').val() || ''} - ${$('#estado').val() || ''}`;
            $('#endereco').val(enderecoCompleto);
        });
    });
</script>
@endpush