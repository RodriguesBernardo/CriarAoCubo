<div class="kanban-card status-{{ $pedido->status }} @if($pedido->arquivado) archived @endif 
     @if($pedido->data_entrega_prevista && \Carbon\Carbon::parse($pedido->data_entrega_prevista)->isPast() && !in_array($pedido->status, ['finalizado', 'entregue'])) urgent @endif" 
     data-id="{{ $pedido->id }}">
     
    <!-- Cabeçalho do Card -->
    <div class="card-header d-flex justify-content-between align-items-center mb-2">
        <h6 class="card-title m-0">
            <span class="badge bg-dark me-2">#{{ $pedido->id }}</span>
            <span>{{ Str::limit($pedido->cliente->nome, 18) }}</span>
        </h6>
        
        <div class="card-actions">
            <div class="btn-group btn-group-sm">
                <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-icon" 
                   title="Visualizar" data-bs-toggle="tooltip">
                    <i class="fas fa-eye"></i>
                </a>
                <button class="btn btn-sm btn-icon archive-btn" 
                        data-id="{{ $pedido->id }}" 
                        data-archived="{{ $pedido->arquivado ? 'true' : 'false' }}"
                        title="{{ $pedido->arquivado ? 'Desarquivar' : 'Arquivar' }}" 
                        data-bs-toggle="tooltip">
                    <i class="fas fa-{{ $pedido->arquivado ? 'box-open' : 'archive' }}"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Barra de Status -->
    <div class="status-bar mb-3"></div>
    
    <!-- Informações Principais -->
    <div class="card-body p-0">
        <!-- Status e Prazo -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge status-badge bg-{{ 
                $pedido->status == 'orcamento' ? 'secondary' : 
                ($pedido->status == 'aberto' ? 'primary' : 
                ($pedido->status == 'em_producao' ? 'info' : 'success')) 
            }}">
                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
            </span>
        
        </div>
        
        <!-- Datas -->
        <div class="timeline mb-3">
            <div class="timeline-item">
                <i class="fas fa-calendar-plus timeline-icon"></i>
                <div class="timeline-content">
                    <small class="text-muted">Criação</small>
                    <p class="mb-0">{{ $pedido->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            @if($pedido->data_entrega_prevista)
            <div class="timeline-item">
                <i class="fas fa-truck timeline-icon"></i>
                <div class="timeline-content">
                    <small class="text-muted">Entrega Prevista</small>
                    <p class="mb-0">{{ $pedido->data_entrega_prevista->format('d/m/Y') }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Valor e Pagamento -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="value-display">
                <small class="text-muted">Valor Total</small>
                <h5 class="mb-0 text-primary card-value">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</h5>
            </div>
            <div class="payment-status">
                <span class="badge bg-{{ $pedido->pago ? 'success' : 'danger' }}">
                    <i class="fas fa-{{ $pedido->pago ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
                    {{ $pedido->pago ? 'Pago' : 'Pendente' }}
                </span>
            </div>
        </div>
        
        <!-- Detalhes Adicionais -->
        <div class="card-details">
            @if($pedido->produtos->count() > 0)
            <div class="detail-item">
                <i class="fas fa-boxes detail-icon"></i>
                <div>
                    <small class="text-muted">Itens</small>
                    <p class="mb-0">{{ $pedido->produtos->count() }} tipos ({{ $pedido->produtos->sum('pivot.quantidade') }} unid.)</p>
                </div>
            </div>
            @endif
            
            @if($pedido->observacoes)
            <div class="detail-item">
                <i class="fas fa-sticky-note detail-icon"></i>
                <div>
                    <small class="text-muted">Observações</small>
                    <p class="mb-0 text-truncate" title="{{ $pedido->observacoes }}" data-bs-toggle="tooltip">
                        {{ Str::limit($pedido->observacoes, 25) }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Rodapé do Card -->
    <div class="card-footer">
        <small class="text-muted">
            Atualizado {{ $pedido->updated_at->diffForHumans() }}
        </small>
    </div>
</div>