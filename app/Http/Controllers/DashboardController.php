<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\ItemPedido;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas básicas
        $totalPedidos = Pedido::count();
        $totalClientes = Cliente::count();
        $totalProdutos = Produto::count();
        
        // Receita do mês atual
        $receitaMes = Pedido::whereMonth('data_pedido', now()->month)
                          ->whereYear('data_pedido', now()->year)
                          ->whereIn('status', ['entregue', 'finalizado'])
                          ->sum('valor_total');
        
        // Receita do mês anterior para cálculo de variação
        $receitaMesAnterior = Pedido::whereMonth('data_pedido', now()->subMonth()->month)
                                  ->whereYear('data_pedido', now()->subMonth()->year)
                                  ->whereIn('status', ['entregue', 'finalizado'])
                                  ->sum('valor_total');
        
        // Cálculo de variações
        $variacaoReceita = $receitaMesAnterior != 0 
                         ? round(($receitaMes - $receitaMesAnterior) / $receitaMesAnterior * 100, 2)
                         : 100;
        
        // Ticket médio
        $ticketMedio = Pedido::whereIn('status', ['entregue', 'finalizado'])
                           ->avg('valor_total');
        
        // Pedidos por mês no ano atual
        $pedidosPorMes = Pedido::select(
                DB::raw('MONTH(data_pedido) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('data_pedido', now()->year)
            ->groupBy('mes')
            ->get();
        
        // Receita por mês no ano atual
        $receitaPorMes = Pedido::select(
            DB::raw('MONTH(data_pedido) as mes'),
            DB::raw('SUM(valor_total) as total')
        )
        ->whereYear('data_pedido', now()->year)
        ->whereIn('status', ['entregue', 'finalizado'])
        ->groupBy('mes')
        ->get();
        
        // Custo por mês no ano atual
        $custoPorMes = ItemPedido::select(
            DB::raw('MONTH(pedidos.data_pedido) as mes'),
            DB::raw('SUM(item_pedido.quantidade * produtos.preco_custo) as total')
        )
        ->join('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
        ->join('produtos', 'item_pedido.produto_id', '=', 'produtos.id')
        ->whereYear('pedidos.data_pedido', now()->year)
        ->whereIn('pedidos.status', ['entregue', 'finalizado'])
        ->groupBy('mes')
        ->get();
        
        // Margem de lucro
        $custoTotal = ItemPedido::select(
            DB::raw('SUM(item_pedido.quantidade * produtos.preco_custo) as total')
        )
        ->join('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
        ->join('produtos', 'item_pedido.produto_id', '=', 'produtos.id')
        ->whereIn('pedidos.status', ['entregue', 'finalizado'])
        ->first()
        ->total ?? 0;
        
        $receitaTotal = Pedido::whereIn('status', ['entregue', 'finalizado'])
                           ->sum('valor_total');
        
        $margemLucro = $receitaTotal > 0 ? round(($receitaTotal - $custoTotal) / $receitaTotal * 100, 2) : 0;
        
        // Status dos pedidos
        $statusPedidos = Pedido::select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->get();
        
        // Últimos pedidos
        $ultimosPedidos = Pedido::with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Pedidos atrasados
        $pedidosAtrasados = Pedido::with('cliente')
            ->whereIn('status', ['aberto', 'em_producao'])
            ->whereDate('data_entrega_prevista', '<', now())
            ->select('*', DB::raw('DATEDIFF(NOW(), data_entrega_prevista) as dias_atraso'))
            ->orderBy('data_entrega_prevista', 'asc')
            ->limit(5)
            ->get();
        
        // Clientes que mais compraram
        $clientesMaisCompraram = Cliente::query()
            ->withCount('pedidos')
            ->select([
                'clientes.id',
                'clientes.nome',
                DB::raw('SUM(pedidos.valor_total) as valor_total')
            ])
            ->leftJoin('pedidos', 'clientes.id', '=', 'pedidos.cliente_id')
            ->whereIn('pedidos.status', ['entregue', 'finalizado'])
            ->groupBy('clientes.id', 'clientes.nome')
            ->having('valor_total', '>', 0)
            ->orderBy('valor_total', 'desc')
            ->limit(5)
            ->get();
        
        // Produtos mais vendidos com margem

        $produtosMaisVendidos = Produto::query()
        ->select([
            'produtos.id',
            'produtos.nome',
            'produtos.descricao',
            'produtos.preco',
            'produtos.preco_custo',
            DB::raw('COALESCE(SUM(item_pedido.quantidade), 0) as vendas_count'),
            DB::raw('COALESCE(SUM(item_pedido.quantidade * item_pedido.preco_unitario), 0) as receita_total'),
            DB::raw('COALESCE(SUM(item_pedido.quantidade * produtos.preco_custo), 0) as custo_total'),
            DB::raw('CASE 
                        WHEN COALESCE(SUM(item_pedido.quantidade * item_pedido.preco_unitario), 0) > 0 
                        THEN ROUND(
                            ((COALESCE(SUM(item_pedido.quantidade * item_pedido.preco_unitario), 0) - 
                            COALESCE(SUM(item_pedido.quantidade * produtos.preco_custo), 0)) / 
                            COALESCE(SUM(item_pedido.quantidade * item_pedido.preco_unitario), 0) * 100), 2)
                        ELSE 0 
                    END as margem')
        ])
        ->leftJoin('item_pedido', 'produtos.id', '=', 'item_pedido.produto_id')
        ->leftJoin('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
        ->whereIn('pedidos.status', ['entregue', 'finalizado'])
        ->groupBy([
            'produtos.id',
            'produtos.nome',
            'produtos.descricao',
            'produtos.preco',
            'produtos.preco_custo'
        ])
        ->having('vendas_count', '>', 0)
        ->orderBy('vendas_count', 'desc')
        ->limit(5)
        ->get();
        
        // Dados dos últimos 6 meses
        $ultimos6Meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $month = $date->month;
            $year = $date->year;
            $monthName = $date->locale('pt_BR')->monthName;
            $monthName = ucfirst(mb_substr($monthName, 0, 3));
            
            // Receita
            $receita = Pedido::whereBetween('data_pedido', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['entregue', 'finalizado'])
                        ->sum('valor_total');
            
            // Custo
            $custo = ItemPedido::select(DB::raw('SUM(item_pedido.quantidade * produtos.preco_custo) as total'))
                            ->join('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
                            ->join('produtos', 'item_pedido.produto_id', '=', 'produtos.id')
                            ->whereBetween('pedidos.data_pedido', [$startOfMonth, $endOfMonth])
                            ->whereIn('pedidos.status', ['entregue', 'finalizado'])
                            ->first()
                            ->total ?? 0;
            
            // Tempo médio de entrega (usando data_entrega_real se disponível)
            $tempoEntrega = Pedido::whereBetween('data_pedido', [$startOfMonth, $endOfMonth])
                                ->where('status', 'entregue')
                                ->select(DB::raw('AVG(DATEDIFF(COALESCE(data_entrega_prevista), data_pedido)) as media'))
                                ->first()
                                ->media ?? 0;
            
            $ultimos6Meses[] = [
                'mes' => $month,
                'ano' => $year,
                'nome' => $monthName,
                'receita' => $receita,
                'custo' => $custo,
                'lucro' => $receita - $custo,
                'tempo_medio_entrega' => round($tempoEntrega)
            ];
        }
        
        // Cálculo de variações para os cards
        $variacaoPedidos = $this->calcularVariacao('pedidos');
        $variacaoClientes = $this->calcularVariacao('clientes');
        $variacaoTicket = $this->calcularVariacao('ticket');
        $variacaoProdutos = $this->calcularVariacao('produtos');
        $variacaoMargem = $this->calcularVariacaoMargem();
        
        return view('dashboard', compact(
            'totalPedidos', 
            'totalClientes',
            'totalProdutos',
            'receitaMes',
            'receitaPorMes',
            'custoPorMes',
            'ticketMedio',
            'pedidosPorMes',
            'statusPedidos',
            'ultimosPedidos',
            'pedidosAtrasados',
            'clientesMaisCompraram',
            'produtosMaisVendidos',
            'variacaoPedidos',
            'variacaoClientes',
            'variacaoReceita',
            'variacaoTicket',
            'variacaoProdutos',
            'variacaoMargem',
            'margemLucro',
            'ultimos6Meses'
        ));
    }
    
    /**
     * Calcula a variação percentual em relação ao mês anterior
     */
    private function calcularVariacao($tipo)
    {
        $mesAtual = now();
        $mesAnterior = now()->subMonth();
        
        switch($tipo) {
            case 'pedidos':
                $atual = Pedido::whereMonth('data_pedido', $mesAtual->month)
                             ->whereYear('data_pedido', $mesAtual->year)
                             ->count();
                $anterior = Pedido::whereMonth('data_pedido', $mesAnterior->month)
                                ->whereYear('data_pedido', $mesAnterior->year)
                                ->count();
                break;
                
            case 'clientes':
                $atual = Cliente::whereMonth('created_at', $mesAtual->month)
                              ->whereYear('created_at', $mesAtual->year)
                              ->count();
                $anterior = Cliente::whereMonth('created_at', $mesAnterior->month)
                                 ->whereYear('created_at', $mesAnterior->year)
                                 ->count();
                break;
                
            case 'ticket':
                $atual = Pedido::whereMonth('data_pedido', $mesAtual->month)
                             ->whereYear('data_pedido', $mesAtual->year)
                             ->whereIn('status', ['entregue', 'finalizado'])
                             ->avg('valor_total');
                $anterior = Pedido::whereMonth('data_pedido', $mesAnterior->month)
                                ->whereYear('data_pedido', $mesAnterior->year)
                                ->whereIn('status', ['entregue', 'finalizado'])
                                ->avg('valor_total');
                break;
                
            case 'produtos':
                $atual = Produto::whereMonth('created_at', $mesAtual->month)
                              ->whereYear('created_at', $mesAtual->year)
                              ->count();
                $anterior = Produto::whereMonth('created_at', $mesAnterior->month)
                                 ->whereYear('created_at', $mesAnterior->year)
                                 ->count();
                break;
                
            default:
                return 0;
        }
        
        if ($anterior == 0) {
            return 100; // Se não havia registros no mês anterior, consideramos 100% de crescimento
        }
        
        return round((($atual - $anterior) / $anterior * 100), 2);
    }
    
    /**
     * Calcula a variação da margem de lucro
     */
    private function calcularVariacaoMargem()
    {
        $mesAtual = now();
        $mesAnterior = now()->subMonth();
        
        // Margem atual
        $receitaAtual = Pedido::whereMonth('data_pedido', $mesAtual->month)
                            ->whereYear('data_pedido', $mesAtual->year)
                            ->whereIn('status', ['entregue', 'finalizado'])
                            ->sum('valor_total');
        
        $custoAtual = ItemPedido::select(DB::raw('SUM(item_pedido.quantidade * produtos.preco_custo) as total'))
                              ->join('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
                              ->join('produtos', 'item_pedido.produto_id', '=', 'produtos.id')
                              ->whereMonth('pedidos.data_pedido', $mesAtual->month)
                              ->whereYear('pedidos.data_pedido', $mesAtual->year)
                              ->whereIn('pedidos.status', ['entregue', 'finalizado'])
                              ->first()
                              ->total ?? 0;
        
        $margemAtual = $receitaAtual > 0 ? (($receitaAtual - $custoAtual) / $receitaAtual) * 100 : 0;
        
        // Margem anterior
        $receitaAnterior = Pedido::whereMonth('data_pedido', $mesAnterior->month)
                               ->whereYear('data_pedido', $mesAnterior->year)
                               ->whereIn('status', ['entregue', 'finalizado'])
                               ->sum('valor_total');
        
        $custoAnterior = ItemPedido::select(DB::raw('SUM(item_pedido.quantidade * produtos.preco_custo) as total'))
                                 ->join('pedidos', 'item_pedido.pedido_id', '=', 'pedidos.id')
                                 ->join('produtos', 'item_pedido.produto_id', '=', 'produtos.id')
                                 ->whereMonth('pedidos.data_pedido', $mesAnterior->month)
                                 ->whereYear('pedidos.data_pedido', $mesAnterior->year)
                                 ->whereIn('pedidos.status', ['entregue', 'finalizado'])
                                 ->first()
                                 ->total ?? 0;
        
        $margemAnterior = $receitaAnterior > 0 ? (($receitaAnterior - $custoAnterior) / $receitaAnterior) * 100 : 0;
        
        if ($margemAnterior == 0) {
            return 100;
        }
        
        return round((($margemAtual - $margemAnterior) / $margemAnterior * 100), 2);
    }
}