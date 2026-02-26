<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroParticular;
use App\Models\Calendario;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardPessoalController extends Controller
{
    public function index()
    {
        $hoje = Carbon::now();
        $user = Auth::user();

        // --- 1. FINANCEIRO (Resumo do Mês Atual) ---
        $financasMes = FinanceiroParticular::where('user_id', $user->id)
            ->whereMonth('data_vencimento', $hoje->month)
            ->whereYear('data_vencimento', $hoje->year)
            ->get();

        $receita = $financasMes->where('tipo', 'receita')->sum('valor');
        $despesa = $financasMes->where('tipo', 'despesa')->sum('valor');
        $saldo = $receita - $despesa;
        
        // Percentual do orçamento gasto (para barra de progresso)
        $percentualGasto = $receita > 0 ? ($despesa / $receita) * 100 : 0;

        // Próximas contas a pagar (Pendentes, próximos 7 dias)
        $contasProximas = FinanceiroParticular::where('user_id', $user->id)
            ->where('tipo', 'despesa')
            ->where('pago', false)
            ->whereBetween('data_vencimento', [$hoje->copy()->startOfDay(), $hoje->copy()->addDays(7)->endOfDay()])
            ->orderBy('data_vencimento')
            ->take(3)
            ->get();

        // --- 2. CALENDÁRIO ---
        // Eventos de Hoje
        $eventosHoje = Calendario::where('user_id', $user->id)
            ->whereDate('inicio', $hoje->today())
            ->orderBy('inicio')
            ->get();

        // Próximos Eventos (Amanhã em diante)
        $proximosEventos = Calendario::where('user_id', $user->id)
            ->whereDate('inicio', '>', $hoje->today())
            ->orderBy('inicio')
            ->take(4)
            ->get();

        // Saudação baseada na hora
        $hora = $hoje->hour;
        $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');

        return view('dashboard_pessoal.index', compact(
            'receita', 'despesa', 'saldo', 'percentualGasto', 'contasProximas',
            'eventosHoje', 'proximosEventos', 'saudacao'
        ));
    }
}