@extends('layouts.app')

@section('title', 'Clientes')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Clientes</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header titlos d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Lista de Clientes</h5>
        <div class="d-flex">
            <form method="GET" class="me-3" style="min-width: 300px;">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Pesquisar..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if($search)
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
            
            <!-- Dropdown de Exportação -->
            <div class="dropdown-export me-2">
                <button class="btn-export" id="exportDropdownBtn">
                    <i class="fas fa-file-export"></i> Exportar
                </button>
                <div class="dropdown-menu-export" id="exportDropdownMenu">
                    <a class="dropdown-item" href="{{ route('clientes.export', ['type' => 'csv']) }}?search={{ $search }}">
                        <i class="fas fa-file-csv"></i> CSV
                    </a>
                    <a class="dropdown-item" href="{{ route('clientes.export', ['type' => 'pdf']) }}?search={{ $search }}">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
            
            <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="">
                    <tr>
                        <th class="py-3 px-4">Nome</th>
                        <th class="py-3 px-4">Contato</th>
                        <th class="py-3 px-4">CPF/CNPJ</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td class="px-4">
                            <div class="fw-semibold">{{ $cliente->nome }}</div>
                            <small class="text-muted">{{ $cliente->email }}</small>
                        </td>
                        <td class="px-4">
                            <div>{{ $cliente->telefone ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $cliente->telefone) : '--' }}</div>
                            @if($cliente->endereco)
                            <small class="text-muted">{{ Str::limit($cliente->endereco, 30) }}</small>
                            @endif
                        </td>
                        <td class="px-4">
                            @if($cliente->cnpj_cpf)
                                @if(strlen(preg_replace('/[^0-9]/', '', $cliente->cnpj_cpf)) === 11)
                                    {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cliente->cnpj_cpf) }}
                                @else
                                    {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cliente->cnpj_cpf) }}
                                @endif
                            @else
                            --
                            @endif
                        </td>
                        <td class="px-4 text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                @if($cliente->telefone)
                                <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $cliente->telefone) }}" 
                                   class="btn btn-success" 
                                   target="_blank"
                                   data-bs-toggle="tooltip" 
                                   title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                @endif
                                <a href="{{ route('clientes.edit', $cliente->id) }}" 
                                   class="btn btn-primary"
                                   data-bs-toggle="tooltip" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger delete-btn"
                                        data-bs-toggle="tooltip" 
                                        title="Excluir"
                                        data-id="{{ $cliente->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="{{ route('clientes.show', $cliente->id) }}" 
                                   class="btn btn-info"
                                   data-bs-toggle="tooltip" 
                                   title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($clientes->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros
            </div>
            <div>
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este cliente?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Configurar modal de exclusão
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const clienteId = this.getAttribute('data-id');
            const form = document.getElementById('deleteForm');
            form.action = `/clientes/${clienteId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

/*     // Máscaras
    document.addEventListener('DOMContentLoaded', function() {
        // Configuração das máscaras será feita no form
    });
 */
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('exportDropdownBtn');
        const dropdownMenu = document.getElementById('exportDropdownMenu');
        const dropdownContainer = document.querySelector('.dropdown-export');

        // Abre/fecha o dropdown
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            dropdownContainer.classList.toggle('show');
        });

        // Fecha o dropdown ao clicar fora
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
            dropdownContainer.classList.remove('show');
        });

        // Previne que o dropdown feche ao clicar dentro dele
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endpush

<style>
    /* Dropdown Container */
    .dropdown-export {
        position: relative;
        display: inline-block;
    }

    /* Botão principal */
    .btn-export {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        font-size: 14px;
        transition: all 0.3s;
    }

    .btn-export:hover {
        background-color: #218838;
    }

    .btn-export i {
        margin-right: 8px;
    }

    /* Menu suspenso */
    .dropdown-menu-export {
        position: absolute;
        right: 0;
        top: 100%;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        min-width: 160px;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    }

    .dropdown-menu-export.show {
        display: block;
    }

    /* Itens do menu */
    .dropdown-item {
        display: block;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
    
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    /* Seta do dropdown */
    .btn-export::after {
        content: "▼";
        font-size: 10px;
        margin-left: 8px;
        transition: transform 0.3s;
    }

    .dropdown-export.show .btn-export::after {
        transform: rotate(180deg);
    }
</style>