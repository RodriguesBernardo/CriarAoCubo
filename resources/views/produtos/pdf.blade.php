<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Produtos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; font-size: 18px; }
        .info { margin-bottom: 10px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8f9fa; text-align: left; padding: 6px; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        .text-center { text-align: center; }
        .img-thumbnail { max-width: 50px; max-height: 50px; }
    </style>
</head>
<body>
    <h1>Relatório de Produtos</h1>
    
    <div class="info">
        <small>Gerado em: {{ now()->format('d/m/Y H:i') }}</small>
        @if($search)
        <p>Filtro aplicado: "{{ $search }}"</p>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Tempo Impressão</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produtos as $produto)
            <tr>
                <td>{{ $produto->id }}</td>
                <td>
                    @if($produto->imagem)
                        <img src="{{ storage_path('app/public/' . $produto->imagem) }}" class="img-thumbnail">
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $produto->nome }}</td>
                <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                <td>{{ $produto->quantidade }}</td>
                <td>{{ $produto->tempo_impressao }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Nenhum produto encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>