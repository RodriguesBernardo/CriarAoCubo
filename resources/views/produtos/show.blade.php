
@extends('layouts.app')

@section('title', 'Produto - ' . $produto->nome)

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Detalhes do Produto</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if($produto->imagem)
                    <img src="{{ route('produtos.imagem', basename($produto->imagem)) }}" alt="{{ $produto->nome }}" class="img-fluid rounded">
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="mt-2">Sem imagem</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h3>{{ $produto->nome }}</h3>
                    <p class="text-muted">{{ $produto->descricao }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <h5>Preço</h5>
                            <p class="h4 text-primary">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Preço de custo</h5>
                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-price" 
                                        data-bs-toggle="tooltip" title="Mostrar/Esconder">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="h4 text-primary price-value" style="display: block;">
                                R$ {{ number_format($produto->preco_custo, 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <h5>Custo por Hora</h5>
                            <p class="h4">R$ {{ number_format($produto->custo_estimado_por_hora, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Custo por Grama</h5>
                            <p class="h4">R$ {{ number_format($produto->custo_estimado_por_grama, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Custo Energia</h5>
                            <p class="h4">R$ {{ number_format($produto->custo_estimado_energia, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h5>Estoque</h5>
                            <p class="h4">{{ $produto->quantidade }} unidades</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h5>Tempo de Impressão</h5>
                            <p class="h4">{{ $produto->tempo_impressao }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Arquivo 3D</h5>
                        @if($produto->arquivo_stl)
                        <a href="{{ route('produtos.downloadStl', $produto->id) }}" class="btn btn-sm btn-info" title="Download STL">
                            <i class="fas fa-download"></i> Baixar Arquivo STL
                        </a>
                            <small class="d-block mt-1 text-muted">{{ basename($produto->arquivo_stl) }}</small>
                        @else
                            <p class="text-muted">Nenhum arquivo cadastrado</p>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('orcamento.produto', $produto->id) }}" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Gerar Orçamento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Alternar visibilidade do preço
        document.querySelectorAll('.toggle-price').forEach(button => {
            button.addEventListener('click', function() {
                const priceValue = this.closest('.col-md-4').querySelector('.price-value');
                const isHidden = priceValue.style.display === 'none';
                
                priceValue.style.display = isHidden ? 'block' : 'none';
                this.querySelector('i').className = isHidden ? 'fas fa-eye' : 'fas fa-eye-slash';
            });
        });
    });
</script>
@endpush