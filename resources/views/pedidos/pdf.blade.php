<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Pedidos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; font-size: 18px; }
        .info { margin-bottom: 10px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8f9fa; text-align: left; padding: 6px; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        .text-center { text-align: center; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Relatório de Pedidos</h1>
    
    <div class="info">
        <small>Gerado em: {{ now()->format('d/m/Y H:i') }}</small>
        @if($search)
        <p>Filtro: "{{ $search }}"</p>
        @endif
        @if($status)
        <p>Status: {{ ucfirst(str_replace('_', ' ', $status)) }}</p>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Entrega</th>
                <th>Status</th>
                <th>Valor</th>
                <th>Pago</th>
                <th>Itens</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
            <tr>
                <td>#{{ $pedido->id }}</td>
                <td>{{ $pedido->cliente->nome }}</td>
                <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                <td>{{ $pedido->data_entrega_prevista ? $pedido->data_entrega_prevista->format('d/m/Y') : 'N/D' }}</td>
                <td>
                    <span class="badge" style="background-color: {{ 
                        $pedido->status == 'orcamento' ? '#6c757d' : 
                        ($pedido->status == 'aberto' ? '#007bff' : 
                        ($pedido->status == 'em_producao' ? '#17a2b8' : 
                        ($pedido->status == 'finalizado' ? '#28a745' : '#343a40'))) 
                    }}; color: white;">
                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                    </span>
                </td>
                <td>R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                <td>{{ $pedido->pago ? 'Sim' : 'Não' }}</td>
                <td>{{ $pedido->produtos->count() }} ({{ $pedido->produtos->sum('pivot.quantidade') }} unid.)</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Nenhum pedido encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>