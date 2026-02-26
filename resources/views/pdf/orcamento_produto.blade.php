<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Orçamento de Produto - Criar³</title>
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
            margin-bottom: 15px;
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
        .titulo-documento {
            font-size: 16pt;
            font-weight: 600;
            text-align: center;
            margin: 15px 0;
            color: #2c3e50;
        }
        .detalhes-produto {
            margin: 15px 0;
        }
        .nome-produto {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .especificacoes {
            font-size: 8pt;
            color: #555;
            margin-bottom: 10px;
        }
        .especificacoes span {
            display: inline-block;
            margin-right: 15px;
        }
        .valores {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .coluna {
            flex: 1;
            padding: 0 5px;
        }
        .linha-valor {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-geral {
            font-weight: 600;
            font-size: 10pt;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }
        .condicoes {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .condicoes h3 {
            font-size: 9pt;
            margin: -10px -10px 5px -10px;
            padding: 5px 10px;
            background-color: #f5f5f5;
        }
        .validade {
            text-align: right;
            margin-top: 15px;
            font-size: 8pt;
        }
        .assinatura {
            margin-top: 30px;
            text-align: center;
        }
        .linha-assinatura {
            width: 50%;
            border-top: 1px solid #000;
            margin: 0 auto;
            padding-top: 5px;
        }
        .footer {
            margin-top: 15px;
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
                <h2>Orçamento</h2>
                <p class="empresa-contato">Data: {{ date('d/m/Y') }}</p>
                <p class="empresa-contato">Validade: {{ date('d/m/Y', strtotime('+7 days')) }}</p>
            </div>
        </div>

        <div class="titulo-documento">ORÇAMENTO DE PRODUTO</div>

        <div class="detalhes-produto">
            <div class="nome-produto">{{ $produto->nome }}</div>
            <div class="especificacoes">
                @if($produto->peso_estimado)
                <span><strong>Peso:</strong> {{ $produto->peso_estimado }}g</span>
                @endif
                @if($produto->tempo_estimado)
                <span><strong>Tempo de produção:</strong> {{ $produto->tempo_estimado }} horas</span>
                @endif
                @if($produto->material)
                <span><strong>Material:</strong> {{ $produto->material }}</span>
                @endif
            </div>

            <div class="valores">
                <div class="coluna">
                    <div class="linha-valor">
                        <span>Quantidade:</span>
                        <span>{{ $quantidade ?? 1 }}</span>
                    </div>
                    <div class="linha-valor">
                        <span>Preço unitário:</span>
                        <span>R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="coluna">
                    <div class="linha-valor">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($produto->preco * ($quantidade ?? 1), 2, ',', '.') }}</span>
                    </div>
                    @if(isset($desconto) && $desconto > 0)
                    <div class="linha-valor">
                        <span>Desconto:</span>
                        <span>- R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="linha-valor total-geral">
                        <span>TOTAL:</span>
                        <span>R$ {{ number_format(($produto->preco * ($quantidade ?? 1)) - ($desconto ?? 0), 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="condicoes">
            <h3>CONDIÇÕES</h3>
            <p><strong>Pagamento:</strong> {{ $formaPagamento ?? 'À vista (PIX, dinheiro ou transferência)' }}</p>
            <p><strong>Prazo de entrega:</strong> {{ $prazoEntrega ?? 'A combinar' }}</p>
            <p><strong>Observações:</strong> {{ $observacoes ?? 'Nenhuma observação adicional.' }}</p>
        </div>

        <div class="validade">
            <p>Orçamento válido até {{ date('d/m/Y', strtotime('+7 days')) }}</p>
        </div>

        <div class="assinatura">
            <div class="linha-assinatura"></div>
            <p>Assinatura do Responsável</p>
        </div>

        <div class="footer">
            <p>Criar³ - Criando ideias em todas as dimensões | criaraocubo@gmail.com</p>
        </div>
    </div>
</body>
</html>