<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Orçamento #{{ $pedido->id }} - Criar³</title>
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
            margin: 5px 0;
        }
        .documento-subtitulo {
            font-size: 8pt;
            text-align: center;
            margin-bottom: 10px;
        }
        .info-box { 
            display: flex; 
            margin-bottom: 10px; 
            font-size: 8pt;
        }
        .info-cliente, .info-documento { 
            flex: 1; 
            padding: 8px; 
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
            margin: 5px 0;
        }
        th { 
            text-align:left;
            background-color: #2c3e50; 
            color: #fff; 
            padding: 6px; 
        }
        td { 
            text-align: left;
            padding: 6px; 
            border-bottom: 1px solid #eee; 
            vertical-align: top;
        }
        td, th {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
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
        .condicoes, .observacoes {
            margin-top: 8px;
            padding: 8px;
            font-size: 8pt;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .condicoes h3, .observacoes h3 {
            font-size: 9pt;
            margin: -8px -8px 5px -8px;
            padding: 5px 8px;
            background-color: #f5f5f5;
        }
        .validade {
            text-align: right;
            margin-top: 10px;
            font-size: 8pt;
        }
        .assinatura {
            margin-top: 20px;
            text-align: center;
        }
        .linha-assinatura {
            width: 50%;
            border-top: 1px solid #000;
            margin: 0 auto;
            padding-top: 5px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 7pt;
            color: #999;
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
                <p class="empresa-contato">@criaraocubo</p>
            </div>
            <p class="empresa-contato">Bento Gonçalves | RS</p>
            <p class="empresa-contato">(54) 9 9194-5373</p>
            <p class="empresa-contato">(54) 9 9625-4837</p>
            <p class="empresa-contato">criaraocubo@gmail.com</p>
        </div>
            <div class="empresa-info">
                <h2>Proposta Comercial</h2>
                <p class="empresa-contato">Nº: {{ $pedido->id + 147}}</p>
                <p class="empresa-contato">Data: {{ $dataEmissao }}</p>
                <p class="empresa-contato">Validade: {{ $validade }}</p>
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
                <h3>ENTREGA</h3>
                <p>Previsão: {{ $pedido->dias_entrega }} dias após a confirmação do pedido</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">Item</th>
                    <th width="25%">Descrição</th>
                    <th width="8%">Qtd</th>
                    <th width="12%">Unitário</th>
                    <th width="15%">Desconto</th>
                    <th width="15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->produtos as $index => $produto)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $produto->nome }}</strong>
                        <div class="detalhe-item">
                            @if($produto->pivot->peso_estimado)
                            <span><strong>Peso:</strong> {{ $produto->pivot->peso_estimado }}g</span>
                            @endif
                            @if($produto->pivot->tempo_estimado)
                            <span><strong>Produção:</strong> {{ $produto->pivot->tempo_estimado }}h</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $produto->pivot->quantidade }}</td>
                    <td>R$ {{ number_format($produto->pivot->preco_unitario, 2, ',', '.') }}</td>
                    <td>
                        @if($produto->pivot->desconto > 0)
                        {{ $produto->pivot->desconto }}
                        @else
                        -
                        @endif
                    </td>
                    <td>R$ {{ number_format(($produto->pivot->preco_unitario * $produto->pivot->quantidade) - ($produto->pivot->desconto ?? 0), 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totais">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span>R$ {{ number_format($pedido->valor_total + $pedido->desconto, 2, ',', '.') }}</span>
            </div>
            @if($pedido->desconto > 0)
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

        <div style="display: flex; margin-top: 8px; gap: 10px;">
            <div class="condicoes" style="flex: 1;">
                <h3>CONDIÇÕES DE PAGAMENTO</h3>
                <p>Pagamento: {{ $formaPagamento }}</p>
            </div>
            <div class="observacoes" style="flex: 1;">
                <h3>OBSERVAÇÕES</h3>
                <p>{{ $pedido->observacoes ?: 'Nenhuma observação.' }}</p>
                <p><strong>PIX para pagamento: 04405318026</strong> (Bernardo Gostenski Rodrigues)</p>
            </div>
        </div>

        <div class="validade">
            <p>Orçamento válido até {{ $validade }}</p>
        </div>

        <div class="assinatura">
            <div class="linha-assinatura"></div>
            <p>Assinatura do Responsável</p>
        </div>

        <div class="footer">
            <p>Criar³ - Criando ideias em todas as dimensões | criaraocubo@gmail.com.br</p>
        </div>
    </div>
</body>
</html>