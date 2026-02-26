<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Financeiro;
use App\Models\ItemPedido;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function index()
    {
        // 1. Coletar dados básicos do financeiro
        $lancamentos = Financeiro::orderBy('data', 'desc')->paginate(10);
        $totalReceitas = Financeiro::where('tipo', 'receita')->sum('valor') ?? 0;
        $totalDespesas = Financeiro::where('tipo', 'despesa')->sum('valor') ?? 0;
        $totalRetirado = Financeiro::where('tipo', 'retirado')->sum('valor') ?? 0;

        // 2. Dados de pedidos
        $pedidosData = Pedido::whereYear('data_pedido', now()->year)
            ->with(['produtos' => function($query) {
                $query->withPivot(['quantidade', 'preco_unitario', 'desconto', 'custo_estimado']);
            }])
            ->get();

        // Pedidos pagos e não pagos
        $pedidosPagos = $pedidosData->where('pago', true)->count();
        $pedidosNaoPagos = $pedidosData->where('pago', false)->count();

        // Status dos pedidos
        $statusPedidos = $pedidosData->groupBy('status')->map->count();
        $statusPedidosLabels = $statusPedidos->keys()->toArray();
        $statusPedidosValues = $statusPedidos->values()->toArray();

        // Cálculo da receita e lucro dos pedidos
        $pedidosPagosData = $pedidosData->where('pago', true);
        $receitaPedidosAnual = $pedidosPagosData->sum('valor_total') ?? 0;

        $lucroPedidos = $pedidosPagosData->sum(function($pedido) {
            return $pedido->produtos->sum(function($produto) {
                $precoVenda = $produto->pivot->preco_unitario - ($produto->pivot->desconto ?? 0);
                $lucroItem = ($precoVenda - $produto->preco_custo) * $produto->pivot->quantidade;
                return max(0, $lucroItem);
            });
        }) ?? 0;

        // Agrupamento por mês para os gráficos
        $receitaPedidosPorMes = $this->initializeMonthlyArray();
        $despesasPorMes = $this->initializeMonthlyArray();
        $lucroPedidosPorMes = $this->initializeMonthlyArray();

        // Preencher receita por mês
        $pedidosPagosData->each(function($pedido) use (&$receitaPedidosPorMes, &$lucroPedidosPorMes) {
            $month = $pedido->data_pedido->format('m');
            
            $receitaPedidosPorMes[$month] += $pedido->valor_total;
            
            $lucroPedidosPorMes[$month] += $pedido->produtos->sum(function($produto) {
                $precoVenda = $produto->pivot->preco_unitario - ($produto->pivot->desconto ?? 0);
                return ($precoVenda - $produto->preco_custo) * $produto->pivot->quantidade;
            });
        });

        // Preencher despesas por mês
        Financeiro::where('tipo', 'despesa')
            ->whereYear('data', now()->year)
            ->get()
            ->each(function($despesa) use (&$despesasPorMes) {
                $month = Carbon::parse($despesa->data)->format('m');
                $despesasPorMes[$month] += $despesa->valor;
            });

        // Calcular margem de lucro por mês
        $margemLucroPorMes = $this->initializeMonthlyArray();
        foreach ($margemLucroPorMes as $month => $value) {
            $receita = $receitaPedidosPorMes[$month] ?? 0;
            $lucro = $lucroPedidosPorMes[$month] ?? 0;
            $margemLucroPorMes[$month] = $receita > 0 ? ($lucro / $receita) * 100 : 0;
        }

        // 3. Top 5 produtos mais lucrativos - Consulta corrigida
        $topProdutos = ItemPedido::select(
                'item_pedido.produto_id',
                DB::raw('SUM((item_pedido.preco_unitario - COALESCE(item_pedido.desconto, 0) - produtos.preco_custo) * item_pedido.quantidade) as lucro_total')
            )
            ->join('produtos', 'produtos.id', '=', 'item_pedido.produto_id')
            ->join('pedidos', 'pedidos.id', '=', 'item_pedido.pedido_id')
            ->where('pedidos.pago', true)
            ->whereYear('pedidos.data_pedido', now()->year)
            ->groupBy('item_pedido.produto_id')
            ->orderByDesc('lucro_total')
            ->limit(5)
            ->with('produto')
            ->get();

        $topProdutosNomes = $topProdutos->pluck('produto.nome')->toArray();
        $topProdutosLucros = $topProdutos->pluck('lucro_total')->toArray();

        // 4. Cálculos financeiros totais
        $lucroReal = ($totalReceitas + $receitaPedidosAnual) - $totalDespesas - $totalRetirado;
        $saldo = $lucroReal;

        // 5. Preparar dados para gráficos
        $chartMeses = [];
        $chartValores = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartMeses[] = Carbon::createFromFormat('!m', $i)->locale('pt_BR')->monthName;
            $chartValores[] = $receitaPedidosPorMes[$month] ?? 0;
        }

        // Gráfico de categorias
        $categoriasData = Financeiro::select('categoria', DB::raw('SUM(valor) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $categoriasLabels = $categoriasData->pluck('categoria')->toArray();
        $categoriasValores = $categoriasData->pluck('total')->toArray();

        return view('financeiro.index', compact(
            'lancamentos',
            'totalReceitas',
            'totalDespesas',
            'totalRetirado',
            'receitaPedidosPorMes',
            'receitaPedidosAnual',
            'lucroPedidos',
            'saldo',
            'lucroReal',
            'chartMeses',
            'chartValores',
            'categoriasLabels',
            'categoriasValores',
            'lucroPedidosPorMes',
            'despesasPorMes',
            'margemLucroPorMes',
            'topProdutosNomes',
            'topProdutosLucros',
            'statusPedidosLabels',
            'statusPedidosValues',
            'pedidosPagos',
            'pedidosNaoPagos'
        ));
    }

    /**
     * Inicializa um array com 12 meses (01 a 12) com valores zerados
     */
    private function initializeMonthlyArray()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[str_pad($i, 2, '0', STR_PAD_LEFT)] = 0;
        }
        return $months;
    }

    public function create()
    {
        return view('financeiro.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data' => 'required|date',
            'tipo' => 'required|in:receita,despesa,retirado',
            'categoria' => 'required|string|max:100',
            'observacoes' => 'nullable|string'
        ]);

        Financeiro::create($request->all());

        return redirect()->route('financeiro.index')
            ->with('success', 'Lançamento cadastrado com sucesso!');
    }

    public function edit(Financeiro $financeiro)
    {
        return view('financeiro.edit', compact('financeiro'));
    }

    public function update(Request $request, Financeiro $financeiro)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data' => 'required|date',
            'tipo' => 'required|in:receita,despesa,retirado',
            'categoria' => 'required|string|max:100',
            'observacoes' => 'nullable|string'
        ]);

        $financeiro->update($request->all());

        return redirect()->route('financeiro.index')
            ->with('success', 'Lançamento atualizado com sucesso!');
    }

    public function destroy(Financeiro $financeiro)
    {
        $financeiro->delete();

        return redirect()->route('financeiro.index')
            ->with('success', 'Lançamento excluído com sucesso!');
    }
}