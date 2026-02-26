@extends('layouts.app')

@section('title', 'Lista de Produtos')
@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h2>Lista de Produtos</h2>
        </div>
        <div class="col-md-6 text-end">
            <!-- Dropdown Exportação -->
            <div class="dropdown d-inline-block me-2">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('produtos.export', ['type' => 'csv']) }}?search={{ request('search') }}">
                        <i class="fas fa-file-csv me-2"></i> CSV
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('produtos.export', ['type' => 'pdf']) }}?search={{ request('search') }}">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </a></li>
                </ul>
            </div>
            
            <a href="{{ route('produtos.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Novo Produto
            </a>
        </div>
    </div>

    <!-- Formulário de Pesquisa -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('produtos.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Pesquisar" 
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> 
                    </button>
                    @if(request('search'))
                        <a href="{{ route('produtos.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Tempo de Impressão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produtos as $produto)
                <tr>
                    <td>{{ $produto->id }}</td>
                    <td>
                        @if($produto->imagem)
                            <img src="{{ route('produtos.imagem', basename($produto->imagem)) }}" alt="{{ $produto->nome }}" class="img-fluid rounded">
                        @else
                            Sem imagem
                        @endif
                    </td>
                    <td>{{ $produto->nome }}</td>
                    <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td>{{ $produto->quantidade }}</td>
                    <td>{{ $produto->tempo_impressao }}</td>
                    <td class="actions-column">
                        <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-sm btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhum produto encontrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação corrigida -->
    <div class="row mt-4">
        <div class="col-md-12 d-flex justify-content-center">
            <nav aria-label="Page navigation">
                {{ $produtos->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicialização do dropdown de exportação
    const exportBtn = document.getElementById('exportDropdown');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = this.nextElementSibling;
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function() {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(function(menu) {
                menu.style.display = 'none';
            });
        });
    }

    // Confirmação para exclusão
    document.querySelectorAll('form[action*="destroy"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if(!confirm('Tem certeza que deseja excluir este produto?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .dropdown-menu {
        min-width: 180px;
    }
    .dropdown-item {
        padding: 0.25rem 1rem;
    }
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }
    .btn-export {
        margin-right: 8px;
    }
    .table img {
        max-width: 50px;
        max-height: 50px;
        border-radius: 4px;
    }
    .table th {
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .actions-column {
        white-space: nowrap;
    }
    /* Estilos para a paginação */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-link {
        color: #0d6efd;
    }
    @media (max-width: 576px) {
        .pagination .page-item .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush