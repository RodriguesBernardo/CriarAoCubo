<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Entrega #{{ $pedido->id }} - Criar³</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        @page { margin: 1cm; }
        body { 
            font-family: 'Poppins', sans-serif; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            font-size: 9pt;
            line-height: 1.3;
        }
        .page { 
            width: 100%; 
            margin: 0 auto; 
            padding: 0; 
        }
        .header { 
            display: flex; 
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .empresa-info { text-align: right; }
        .empresa-info h2 { 
            color: #2c3e50; 
            margin: 0; 
            font-size: 14pt;
        }
        .empresa-contato {
            font-size: 8pt; 
            margin: 2px 0;
        }
        .documento-titulo { 
            font-size: 16pt; 
            font-weight: 600; 
            text-align: center;
            margin: 15px 0;
            text-transform: uppercase;
            color: #2c3e50;
        }
        .info-box { 
            display: flex; 
            margin-bottom: 10px; 
            font-size: 8pt;
        }
        .info-cliente, .info-documento { 
            flex: 1; 
            padding: 8px; 
            border: 1px solid #eee;
            border-radius: 4px;
            margin: 5px;
        }
        .info-box h3 {
            font-size: 9pt; 
            margin: -8px -8px 5px -8px; 
            padding: 5px 8px;
            background-color: #f5f5f5;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 8pt;
            margin: 15px 0;
        }
        th { 
            background-color: #2c3e50; 
            color: #fff; 
            padding: 6px; 
            text-align: left;
        }
        td { 
            padding: 6px; 
            border-bottom: 1px solid #eee; 
            vertical-align: top;
        }
        .detalhe-item {
            font-size: 7.5pt;
            color: #555;
            margin-top: 2px;
        }
        .detalhe-item span {
            display: inline-block;
            margin-right: 8px;
        }
        .totais {
            text-align: right;
            margin-top: 8px;
            font-size: 9pt;
        }
        .total-row { 
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }
        .total-label { font-weight: 500; }
        .assinaturas-container {
            margin-top: 40px;
            text-align: center;
        }
        .assinatura {
            display: inline-block;
            width: 40%;
            margin: 0 10px;
            text-align: center;
        }
        .linha-assinatura {
            border-top: 1px solid #000;
            width: 100%;
            margin: 0 auto;
            padding-top: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .detalhes-entrega {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .detalhes-entrega h3 {
            font-size: 9pt;
            margin: -10px -10px 5px -10px;
            padding: 5px 10px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div>
                <div style="margin-bottom: 5px;">
                    <h2 style="margin: 0; padding: 0; line-height: 1;">Criar³</h2>
                    <h5 style="margin: 0; padding: 0; font-weight: 400; font-size: 9pt; color: #555;">Criando ideias em todas as dimensões</h5>
                </div>
                <p class="empresa-contato">Bento Gonçalves | RS</p>
                <p class="empresa-contato">(54) 9 9194-5373</p>
                <p class="empresa-contato">(54) 9 9625-4837</p>
                <p class="empresa-contato">criaraocubo@gmail.com</p>
                <p class="empresa-contato">@criaraocubo</p>
            </div>
            <div class="empresa-info">
                <h2>COMPROVANTE DE ENTREGA</h2>
                <p class="empresa-contato">Nº: {{ $pedido->id + 100}}</p>
                <p class="empresa-contato">Data: {{ $dataEmissao }}</p>
                <p class="empresa-contato">Status: <strong>Finalizado</strong></p>
            </div>
        </div>

        <div class="info-box">
            <div class="info-cliente">
                <h3>CLIENTE</h3>
                <p><strong>{{ $pedido->cliente->nome }}</strong></p>
                <p>Tel: {{ $pedido->cliente->telefone ?? 'Não informado' }}</p>
                <p>Email: {{ $pedido->cliente->email ?? 'Não informado' }}</p>
            </div>
            <div class="info-documento">
                <h3>DETALHES DO PEDIDO</h3>
                <p>Pedido #: {{ $pedido->id + 100}}</p>
                <p>Data Pedido: {{ $pedido->data_pedido->format('d/m/Y') }}</p>
                <p>Status Pagamento: {{ $pedido->pago ? 'Pago' : 'Pendente' }}</p>
            </div>
        </div>

        <div class="detalhes-entrega">
            <h3>DETALHES DA ENTREGA</h3>
            <p>Data da Entrega: {{ $dataEntrega }}</p>
            <p>Local de Entrega: {{ $pedido->cliente->endereco ?? 'A combinar' }}</p>
            <p>Observações: {{ $pedido->observacoes ?: 'Nenhuma observação' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">Item</th>
                    <th width="45%">Descrição</th>
                    <th width="10%">Qtd</th>
                    <th width="15%">Unitário</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->produtos as $index => $produto)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $produto->nome }}</strong>
                        <div class="detalhe-item">
                            @if($produto->pivot->observacoes)
                            <span><strong>Obs:</strong> {{ $produto->pivot->observacoes }}</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $produto->pivot->quantidade }}</td>
                    <td>R$ {{ number_format($produto->pivot->preco_unitario, 2, ',', '.') }}</td>
                    <td>Entregue</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totais">
            @if($pedido->desconto > 0)
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span>R$ {{ number_format($pedido->valor_total + $pedido->desconto, 2, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Desconto:</span>
                <span>- R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row" style="font-weight: 600;">
                <span class="total-label">Total:</span>
                <span>R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="assinaturas-container">
            <div class="assinatura">
                <div class="linha-assinatura"></div>
                <p>{{ $responsavelEntrega }}</p>
                <p>Responsável pela Entrega</p>
            </div>
            <div class="assinatura">
                <div class="linha-assinatura"></div>
                <p>{{ $responsavelRecebimento }}</p>
                <p>Cliente/Recebedor</p>
            </div>
        </div>

        <div class="footer">
            <p>Criar³ - Criando ideias em todas as dimensões | criaraocubo@gmail.com.br</p>
            <p>Este documento comprova a entrega dos itens listados acima em perfeitas condições.</p>
        </div>
    </div>
</body>
</html>