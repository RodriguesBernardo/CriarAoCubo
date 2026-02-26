@extends('layouts.app')

@section('title', 'Criar Novo Produto')
@section('content')
<div class="container">
    <h2>Criar Novo Produto</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data" id="form-produto">
        @csrf
        
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="custo_estimado_por_hora" class="form-label">Custo por Hora (R$)</label>
                <input type="number" step="0.01" class="form-control custo-input" id="custo_estimado_por_hora" name="custo_estimado_por_hora" required>
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="custo_estimado_por_grama" class="form-label">Custo por Grama (R$)</label>
                <input type="number" step="0.01" class="form-control custo-input" id="custo_estimado_por_grama" name="custo_estimado_por_grama" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="custo_estimado_energia" class="form-label">Custo Energia (R$)</label>
                <input type="number" step="0.01" class="form-control custo-input" id="custo_estimado_energia" name="custo_estimado_energia" required>
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="preco_custo" class="form-label">Preço de Custo (R$)</label>
                <input type="number" step="0.01" class="form-control custo-input" id="preco_custo" name="preco_custo" required>
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="tempo_impressao" class="form-label">Tempo de Impressão</label>
                <input type="text" class="form-control custo-input" id="tempo_impressao" 
                    name="tempo_impressao" placeholder="00:00:00" required>
                <small class="text-muted">Formato HH:MM:SS</small>
            </div>

            <div class="col-md-4 mb-3">
                <label for="margem_lucro" class="form-label">Margem de Lucro (%)</label>
                <input type="number" step="0.01" class="form-control" id="margem_lucro" name="margem_lucro" value="100" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="preco" class="form-label">Preço de Venda (R$)</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                <small class="text-muted">Calculado automaticamente</small>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade em Estoque</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" required>
        </div>
        
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem do Produto</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
            <small class="text-muted">Formatos aceitos: jpeg, png, jpg (máx. 5MB)</small>
            <div id="aviso-imagem" class="text-danger d-none">Imagem muito grande! Máximo 5MB.</div>
        </div>
        
        <div class="mb-3">
            <label for="arquivo_stl" class="form-label">Arquivo 3D (STL/OBJ/3DS)</label>
            <input type="file" class="form-control" id="arquivo_stl" name="arquivo_stl" 
                   accept=".stl,.obj,.3ds" onchange="validarTamanhoArquivo(this)">
            <small class="text-muted">Tamanho máximo: 40MB (arquivos grandes podem demorar)</small>
            <div id="aviso-tamanho" class="text-danger d-none">Arquivo muito grande! Máximo 40MB.</div>
        </div>
        
        <button type="submit" class="btn btn-primary" id="btn-submit">Salvar Produto</button>
        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do formulário
    const form = document.getElementById('form-produto');
    const precoCustoInput = document.getElementById('preco_custo');
    const custoEnergiaInput = document.getElementById('custo_estimado_energia');
    const custoPorHoraInput = document.getElementById('custo_estimado_por_hora');
    const tempoImpressaoInput = document.getElementById('tempo_impressao');
    const margemLucroInput = document.getElementById('margem_lucro');
    const precoVendaInput = document.getElementById('preco');
    const inputsCusto = document.querySelectorAll('.custo-input');
    const btnSubmit = document.getElementById('btn-submit');

    // Máscara para o campo de tempo (HH:MM:SS)
    tempoImpressaoInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        
        // Formata HH:MM:SS
        if (value.length > 4) {
            value = value.substring(0, 2) + ':' + value.substring(2, 4) + ':' + value.substring(4);
        } else if (value.length > 2) {
            value = value.substring(0, 2) + ':' + value.substring(2);
        }
        
        this.value = value;
        calcularPrecoVenda();
    });

    // Função para converter tempo HH:MM:SS em horas decimais
    function converterTempoParaHoras(tempo) {
        if (!tempo) return 0;
        
        const cleanTime = tempo.replace(/\D/g, '');
        const paddedTime = cleanTime.padEnd(6, '0');
        
        const horas = parseInt(paddedTime.substring(0, 2)) || 0;
        const minutos = parseInt(paddedTime.substring(2, 4)) || 0;
        const segundos = parseInt(paddedTime.substring(4, 6)) || 0;
        
        return horas + (minutos / 60) + (segundos / 3600);
    }

    // Função para calcular o custo total
    function calcularCustoTotal() {
        const precoCusto = parseFloat(precoCustoInput.value) || 0;
        const custoEnergia = parseFloat(custoEnergiaInput.value) || 0;
        const custoPorHora = parseFloat(custoPorHoraInput.value) || 0;
        const tempoImpressao = converterTempoParaHoras(tempoImpressaoInput.value);
        
        const custoImpressao = custoPorHora * tempoImpressao;
        return precoCusto + custoEnergia + custoImpressao;
    }

    // Função para calcular o preço de venda
    function calcularPrecoVenda() {
        const custoTotal = calcularCustoTotal();
        const margemLucro = parseFloat(margemLucroInput.value) || 0;
        
        const precoSugerido = custoTotal * (1 + (margemLucro / 100));
        precoVendaInput.value = precoSugerido.toFixed(2);
    }

    // Função para calcular a margem de lucro
    function calcularMargemLucro() {
        const custoTotal = calcularCustoTotal();
        const precoVenda = parseFloat(precoVendaInput.value) || 0;
        
        if (custoTotal > 0) {
            const margemCalculada = ((precoVenda - custoTotal) / custoTotal) * 100;
            margemLucroInput.value = margemCalculada.toFixed(2);
        } else {
            margemLucroInput.value = "0";
        }
    }

    // Validação de tamanho de arquivo
    function validarTamanhoArquivo(input) {
        const aviso = document.getElementById('aviso-tamanho');
        const maxSize = 40 * 1024 * 1024; // 40MB
        
        if (input.files[0] && input.files[0].size > maxSize) {
            aviso.classList.remove('d-none');
            input.value = '';
            btnSubmit.disabled = true;
        } else {
            aviso.classList.add('d-none');
            btnSubmit.disabled = false;
        }
    }

    // Validação de tamanho para imagem
    document.getElementById('imagem').addEventListener('change', function() {
        const aviso = document.getElementById('aviso-imagem');
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (this.files[0] && this.files[0].size > maxSize) {
            aviso.classList.remove('d-none');
            this.value = '';
            btnSubmit.disabled = true;
        } else {
            aviso.classList.add('d-none');
            btnSubmit.disabled = false;
        }
    });

    // Event listeners para cálculos
    inputsCusto.forEach(input => {
        input.addEventListener('input', calcularPrecoVenda);
    });

    margemLucroInput.addEventListener('input', calcularPrecoVenda);
    precoVendaInput.addEventListener('input', calcularMargemLucro);

    // Calcula inicialmente
    calcularPrecoVenda();
});
</script>
@endpush

@endsection