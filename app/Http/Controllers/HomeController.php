<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\FinanceiroParticular;
use App\Models\Calendario;
use App\Models\ItemPedido; // Para cálculo de lucro
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $hoje = Carbon::now();
        $user = Auth::user();

        // --- 1. DADOS DA EMPRESA (CRIAR³) ---
        
        // Faturamento (Vendas Entregues/Finalizadas)
        $faturamentoEmpresa = Pedido::whereMonth('data_pedido', $hoje->month)
            ->whereYear('data_pedido', $hoje->year)
            ->whereIn('status', ['entregue', 'finalizado'])
            ->sum('valor_total');

        // Pedidos em Produção (O que precisa de atenção agora)
        $pedidosEmProducao = Pedido::where('status', 'em_producao')->count();
        
        // Pedidos Atrasados
        $pedidosAtrasados = Pedido::whereIn('status', ['aberto', 'em_producao'])
            ->whereDate('data_entrega_prevista', '<', $hoje)
            ->count();

        // --- 2. DADOS PESSOAIS ---

        // Saldo Particular (Receitas - Despesas do mês)
        $financas = FinanceiroParticular::where('user_id', $user->id)
            ->whereMonth('data_vencimento', $hoje->month)
            ->whereYear('data_vencimento', $hoje->year)
            ->get();
        
        // Calculando Lucro da Criar3 para somar no pessoal (Lógica que já fizemos)
        $pedidosPagosMes = Pedido::whereMonth('data_pedido', $hoje->month)
            ->whereYear('data_pedido', $hoje->year)
            ->where('pago', true)
            ->with('produtos')
            ->get();

        $lucroCriar3 = $pedidosPagosMes->sum(function($pedido) {
            return $pedido->produtos->sum(function($produto) {
                $precoVenda = $produto->pivot->preco_unitario - ($produto->pivot->desconto ?? 0);
                return max(0, ($precoVenda - $produto->preco_custo) * $produto->pivot->quantidade);
            });
        });

        $receitaPessoal = $financas->where('tipo', 'receita')->sum('valor') + $lucroCriar3;
        $despesaPessoal = $financas->where('tipo', 'despesa')->sum('valor');
        $saldoPessoal = $receitaPessoal - $despesaPessoal;

        // Contas a Pagar (Próximos 7 dias)
        $contasProximas = FinanceiroParticular::where('user_id', $user->id)
            ->where('tipo', 'despesa')
            ->where('pago', false)
            ->whereBetween('data_vencimento', [$hoje->copy()->startOfDay(), $hoje->copy()->addDays(7)->endOfDay()])
            ->orderBy('data_vencimento')
            ->take(3)
            ->get();

        // --- 3. AGENDA (Geral) ---
        $eventosHoje = Calendario::where('user_id', $user->id)
            ->whereDate('inicio', $hoje->today())
            ->orderBy('inicio')
            ->get();

        // Saudação
        $hora = $hoje->hour;
        $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');

        return view('home.index', compact(
            'faturamentoEmpresa', 'pedidosEmProducao', 'pedidosAtrasados',
            'saldoPessoal', 'contasProximas', 'eventosHoje', 'saudacao'
        ));
    }
}