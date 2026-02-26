<?php

namespace App\Http\Controllers;

use App\Models\FinanceiroParticular;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FinanceiroParticularController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes', now()->month);
        $ano = $request->get('ano', now()->year);
        $filtroCategoria = $request->get('categoria_filtro');
        $filtroResponsavel = $request->get('responsavel_filtro');

        // 1. DADOS PARTICULARES (Sua tabela pessoal)
        $query = FinanceiroParticular::where('user_id', Auth::id())
            ->whereMonth('data_vencimento', $mes)
            ->whereYear('data_vencimento', $ano);

        if ($filtroCategoria) {
            $query->where('categoria', $filtroCategoria);
        }

        if ($filtroResponsavel) {
            if ($filtroResponsavel == 'Ambos') {
                $query->where('responsavel', 'Ambos');
            } else {
                $query->where('responsavel', $filtroResponsavel);
            }
        }
        $movimentacoes = $query->orderBy('data_vencimento')->get();

        $receitaParticular = $movimentacoes->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $movimentacoes->where('tipo', 'despesa')->sum('valor');

        // 2. DADOS DA EMPRESA (Cálculo do Lucro Criar³)
        // Filtramos os pedidos pagos DO MÊS SELECIONADO no filtro
        $pedidosMes = Pedido::whereMonth('data_pedido', $mes)
            ->whereYear('data_pedido', $ano)
            ->where('pago', true)
            ->with(['produtos']) // Carrega produtos para pegar custo e preço
            ->get();

        $lucroCriar3 = $pedidosMes->sum(function ($pedido) {
            return $pedido->produtos->sum(function ($produto) {
                // Preço de Venda (com desconto) - Preço de Custo * Quantidade
                $precoVenda = $produto->pivot->preco_unitario - ($produto->pivot->desconto ?? 0);
                $custo = $produto->preco_custo;
                $lucroItem = ($precoVenda - $custo) * $produto->pivot->quantidade;
                return max(0, $lucroItem);
            });
        });

        // 3. TOTAIS GERAIS (Soma tudo)
        $totalReceitas = $receitaParticular + $lucroCriar3;
        $saldo = $totalReceitas - $totalDespesas;


        $gastosPorCategoria = $movimentacoes->where('tipo', 'despesa')
            ->groupBy('categoria')
            ->map(fn($row) => $row->sum('valor'));

    
        $dadosAnuais = FinanceiroParticular::selectRaw('MONTH(data_vencimento) as mes, tipo, SUM(valor) as total')
            ->where('user_id', Auth::id())
            ->whereYear('data_vencimento', $ano)
            ->groupBy('mes', 'tipo')
            ->get();

        $receitasAno = array_fill(1, 12, 0);
        $despesasAno = array_fill(1, 12, 0);

        foreach ($dadosAnuais as $dado) {
            if ($dado->tipo == 'receita') $receitasAno[$dado->mes] = $dado->total;
            if ($dado->tipo == 'despesa') $despesasAno[$dado->mes] = $dado->total;
        }

        $receitasAno[$mes] += $lucroCriar3;

        $todasCategorias = FinanceiroParticular::where('user_id', Auth::id())
            ->distinct()
            ->pluck('categoria')
            ->sort();

        return view('financeiro_particular.index', compact(
            'movimentacoes',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'gastosPorCategoria',
            'mes',
            'ano',
            'todasCategorias',
            'filtroCategoria',
            'receitasAno',
            'despesasAno',
            'lucroCriar3',
            'receitaParticular',
            'filtroResponsavel'
        ));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'descricao' => 'required',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'tipo' => 'required',
            'categoria' => 'required',
            'responsavel' => 'required',
            'repeticao' => 'required',
            'qtd_parcelas' => 'nullable|integer|required_if:repeticao,parcelada'
        ]);

        $grupoId = Str::uuid();
        $dataBase = Carbon::parse($dados['data_vencimento']);

        if ($dados['repeticao'] === 'parcelada') {
            for ($i = 1; $i <= $dados['qtd_parcelas']; $i++) {
                FinanceiroParticular::create([
                    'user_id' => Auth::id(),
                    'grupo_id' => $grupoId,
                    'descricao' => $dados['descricao'],
                    'valor' => $dados['valor'],
                    'data_vencimento' => $dataBase->copy()->addMonths($i - 1),
                    'tipo' => $dados['tipo'],
                    'categoria' => $dados['categoria'],
                    'responsavel' => $dados['responsavel'],
                    'is_parcelado' => true,
                    'parcela_atual' => $i,
                    'total_parcelas' => $dados['qtd_parcelas'],
                    'pago' => false
                ]);
            }
        } elseif ($dados['repeticao'] === 'fixa') {
            FinanceiroParticular::create([
                'user_id' => Auth::id(),
                'grupo_id' => $grupoId,
                'descricao' => $dados['descricao'],
                'valor' => $dados['valor'],
                'data_vencimento' => $dados['data_vencimento'],
                'tipo' => $dados['tipo'],
                'categoria' => $dados['categoria'],
                'responsavel' => $dados['responsavel'],
                'is_fixo' => true,
                'pago' => false
            ]);
        } else {
            FinanceiroParticular::create([
                'user_id' => Auth::id(),
                'grupo_id' => $grupoId,
                'descricao' => $dados['descricao'],
                'valor' => $dados['valor'],
                'data_vencimento' => $dados['data_vencimento'],
                'tipo' => $dados['tipo'],
                'categoria' => $dados['categoria'],
                'responsavel' => $dados['responsavel'],
                'pago' => false
            ]);
        }

        return redirect()->back()->with('success', 'Lançamento adicionado!');
    }

    public function destroy($id)
    {
        $fin = FinanceiroParticular::where('user_id', Auth::id())->findOrFail($id);
        $fin->delete();
        return redirect()->back()->with('success', 'Removido!');
    }

    public function pagar($id)
    {
        $fin = FinanceiroParticular::where('user_id', Auth::id())->findOrFail($id);
        $fin->pago = !$fin->pago;
        $fin->save();
        return response()->json(['success' => true, 'pago' => $fin->pago]);
    }

    public function update(Request $request, $id)
    {
        $fin = FinanceiroParticular::where('user_id', Auth::id())->findOrFail($id);

        $dados = $request->validate([
            'descricao' => 'required',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'tipo' => 'required',
            'categoria' => 'required',
            'responsavel' => 'required',
        ]);

        $fin->update($dados);

        return redirect()->back()->with('success', 'Atualizado com sucesso!');
    }
}
