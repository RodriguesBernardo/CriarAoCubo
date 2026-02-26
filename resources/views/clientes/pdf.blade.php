<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; text-align: center; }
        .info { margin-bottom: 20px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; text-align: left; padding: 8px; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>Relatório de Clientes</h1>
    
    <div class="info">
        <small>Gerado em: {{ now()->format('d/m/Y H:i') }}</small>
        @if($search)
        <p>Filtro aplicado: "{{ $search }}"</p>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>CPF/CNPJ</th>
                <th>Endereço</th>
                <th>Cadastrado em</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clientes as $cliente)
            <tr>
                <td>{{ $cliente->nome }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->telefone ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $cliente->telefone) : '--' }}</td>
                <td>
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
                <td>{{ $cliente->endereco ?? '--' }}</td>
                <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Nenhum cliente encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>