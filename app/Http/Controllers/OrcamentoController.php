<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\Produto;

class OrcamentoController extends Controller
{
    public function gerarPdf($id)
    {
        $pedido = Pedido::with('cliente', 'produtos')->findOrFail($id);
        
        $data = [
            'pedido' => $pedido,
            'empresa' => [
                'nome' => 'Criar3',
                'cidade' => 'Bento Gonçalves - RS',
                'telefone' => '(54) 9 9194-5373',
                'email' => 'criaraocubo@gmail.com',
            ],
            'dataEmissao' => now()->format('d/m/Y'),
            'validade' => now()->addDays(3)->format('d/m/Y'),
            'condicoesPagamento' => 'Pagamento à vista',
            'formaPagamento' => '50% do valor no pedido e 50% na entrega'
        ];
        
        $pdf = Pdf::loadView('pdf.orcamento', $data)
                 ->setPaper('a4')
                 ->setOption('defaultFont', 'Helvetica');
        
        return $pdf->download("Orcamento {$pedido->cliente->nome}.pdf");
    }
    
    public function gerarOrcamentoProduto($id)
    {
        $produto = Produto::findOrFail($id);

        $data = [
            'produto' => $produto,
            'empresa' => [
                'nome' => 'Criar3',
                'cidade' => 'Bento Gonçalves - RS',
                'telefone' => '(54) 9 9194-5373',
                'email' => 'criaraocubo@gmail.com',
            ],
            'dataEmissao' => now()->format('d/m/Y'),
            'validade' => now()->addDays(7)->format('d/m/Y'),
            'condicoesPagamento' => 'Pagamento à vista',
            'formaPagamento' => '50% no pedido e 50% na entrega'
        ];

        $pdf = Pdf::loadView('pdf.orcamento_produto', $data)
                ->setPaper('a4')
                ->setOption('defaultFont', 'Helvetica');

        return $pdf->download("Orcamento do Produto: {$produto->nome}.pdf");
    }
}
