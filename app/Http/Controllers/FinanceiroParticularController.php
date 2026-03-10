<?php

namespace App\Http\Controllers;

use App\Models\FinanceiroParticular;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use OfxParser\Parser;

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

    public function importarOfx(Request $request)
    {
        $request->validate([
            'arquivo_ofx' => 'required|file',
            'responsavel_ofx' => 'required'
        ]);

        try {
            $file = $request->file('arquivo_ofx');

            $ofxParser = new Parser();
            $ofx = $ofxParser->loadFromFile($file->getPathname());
            $bankAccount = reset($ofx->bankAccounts);
            $transactions = $bankAccount->statement->transactions;

            $count = 0;
            $grupoId = Str::uuid();

            foreach ($transactions as $transaction) {
                $tipo = $transaction->type === 'CREDIT' ? 'receita' : 'despesa';
                $valor = abs($transaction->amount);
                $data = $transaction->date->format('Y-m-d');
                $descricao = $transaction->memo ?? 'Lançamento Importado';

                $jaExiste = FinanceiroParticular::where('user_id', Auth::id())
                    ->where('valor', $valor)
                    ->where('data_vencimento', $data)
                    ->where('tipo', $tipo)
                    ->exists();

                if (!$jaExiste) {
                    FinanceiroParticular::create([
                        'user_id' => Auth::id(),
                        'grupo_id' => Str::uuid(),
                        'descricao' => Str::limit($descricao, 255),
                        'valor' => $valor,
                        'data_vencimento' => $data,
                        'tipo' => $tipo,
                        'categoria' => 'Outros',
                        'responsavel' => $request->responsavel_ofx,
                        'pago' => true
                    ]);
                    $count++;
                }
            }

            return redirect()->back()->with('success', "Importação concluída! {$count} novos lançamentos foram adicionados.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['erro' => 'Erro ao ler o arquivo OFX: ' . $e->getMessage()]);
        }
    }

    public function analisarOfx(Request $request)
    {
        $request->validate(['arquivo_ofx' => 'required|file']);

        try {
            $content = file_get_contents($request->file('arquivo_ofx')->getPathname());

            // 1. Extração via Regex
            preg_match_all('/<STMTTRN>([\s\S]*?)(?=<\/?STMTTRN>|<\/BANKTRANLIST>)/', $content, $matches);

            if (empty($matches[0])) {
                return response()->json(['success' => false, 'message' => 'Nenhuma transação identificada no arquivo.']);
            }

            $lancamentos = [];
            $descricoesParaIA = [];

            foreach ($matches[0] as $index => $t) {
                preg_match('/<DTPOSTED>(\d{4})(\d{2})(\d{2})/', $t, $dt);
                $data = $dt ? "{$dt[1]}-{$dt[2]}-{$dt[3]}" : date('Y-m-d');

                preg_match('/<TRNAMT>([-\d\.]+)/', $t, $amt);
                $valorBruto = $amt ? (float) $amt[1] : 0;

                preg_match('/<MEMO>(.*?)(?:<|\r|\n|$)/', $t, $memo);
                $descricao = $memo ? trim($memo[1]) : 'Lançamento Importado';

                $lancamentos[$index] = [
                    'id_temp' => $index,
                    'data_vencimento' => $data,
                    'descricao' => \Illuminate\Support\Str::limit($descricao, 255),
                    'valor' => abs($valorBruto),
                    'tipo' => $valorBruto > 0 ? 'receita' : 'despesa',
                    'categoria' => 'Outros' // Categoria padrão blindada
                ];

                $descricoesParaIA[] = [
                    'id' => $index,
                    'descricao' => $descricao,
                    'valor' => $valorBruto
                ];
            }

            // 2. Integração com IA (Com Debug e bypass de SSL para localhost)
            $debugIA = 'Não executado';
            try {
                $apiKey = env('GEMINI_API_KEY');

                if (!$apiKey) {
                    $debugIA = "ERRO: Chave GEMINI_API_KEY não encontrada no arquivo .env.";
                } else {
                    $prompt = "Você é um assistente financeiro. Categorize as seguintes transações. 
                    Retorne APENAS um array JSON válido, sem formatação markdown, com objetos contendo 'id' e 'categoria'.
                    Categorias permitidas estritamente: Casa, Alimentação, Transporte, Lazer, Saúde, Esporte, Assinaturas, Estudos, Empreendimento, Roupas, Internet, Salário, Fatura, Outros.
                    
                    Regras de contexto:
                    - Gastos com 'Natsport' ou academias são 'Esporte'.
                    - Gastos em postos de combustível, Uber, ou manutenção da Yamaha FZ25 são 'Transporte'.
                    - Compras de filamento, resina, Tinkercad, ou peças 3D são 'Empreendimento'.
                    - Gastos com veterinário ou itens para cachorro são 'Casa'.
                    - Estabelecimentos locais de Bento Gonçalves focados em comida são 'Alimentação'.
                    
                    Transações para categorizar: " . json_encode($descricoesParaIA);
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->timeout(30)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey, [
                            'contents' => [['parts' => [['text' => $prompt]]]]
                        ]);

                    if ($response->successful()) {
                        $iaText = $response->json('candidates.0.content.parts.0.text');
                        $debugIA = "RESPOSTA BRUTA DA IA: " . $iaText;

                        // Garante que vai extrair apenas o array JSON, ignorando textos em volta
                        preg_match('/\[.*\]/s', $iaText, $jsonMatches);
                        $cleanJson = $jsonMatches[0] ?? $iaText;

                        $resultadoIA = json_decode(trim($cleanJson), true);

                        if (is_array($resultadoIA)) {
                            foreach ($resultadoIA as $item) {
                                if (isset($item['id']) && isset($item['categoria']) && isset($lancamentos[(int)$item['id']])) {
                                    $lancamentos[(int)$item['id']]['categoria'] = $item['categoria'];
                                }
                            }
                            $debugIA .= " | SUCESSO: " . count($resultadoIA) . " itens processados.";
                        } else {
                            $debugIA .= " | ERRO: Falha ao fazer o json_decode. Formato inválido.";
                        }
                    } else {
                        $debugIA = "ERRO DA API (HTTP " . $response->status() . "): " . $response->body();
                    }
                }
            } catch (\Exception $e) {
                $debugIA = "EXCEÇÃO NO PHP AO CHAMAR IA: " . $e->getMessage();
            }

            // Retornamos o debugIA junto para o frontend
            return response()->json([
                'success' => true,
                'dados' => array_values($lancamentos),
                'debug_ia' => $debugIA
            ]);
        } catch (\Exception $e) {
            // Este catch pega erros críticos na leitura do arquivo
            return response()->json(['success' => false, 'message' => 'Erro interno ao processar arquivo: ' . $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function salvarLote(Request $request)
    {
        $dados = $request->validate([
            'lancamentos' => 'required|array',
            'lancamentos.*.descricao' => 'required|string',
            'lancamentos.*.valor' => 'required|numeric',
            'lancamentos.*.data_vencimento' => 'required|date',
            'lancamentos.*.tipo' => 'required|string',
            'lancamentos.*.categoria' => 'required|string',
            'responsavel' => 'required|string'
        ]);

        $grupoId = Str::uuid();
        $count = 0;

        foreach ($dados['lancamentos'] as $lancamento) {
            $existe = FinanceiroParticular::where('user_id', Auth::id())
                ->where('valor', $lancamento['valor'])
                ->where('data_vencimento', $lancamento['data_vencimento'])
                ->where('tipo', $lancamento['tipo'])
                ->exists();

            if (!$existe) {
                FinanceiroParticular::create([
                    'user_id' => Auth::id(),
                    'grupo_id' => $grupoId,
                    'descricao' => $lancamento['descricao'],
                    'valor' => $lancamento['valor'],
                    'data_vencimento' => $lancamento['data_vencimento'],
                    'tipo' => $lancamento['tipo'],
                    'categoria' => $lancamento['categoria'],
                    'responsavel' => $dados['responsavel'],
                    'pago' => true
                ]);
                $count++;
            }
        }

        return response()->json(['success' => true, 'message' => "{$count} lançamentos importados com sucesso!"]);
    }
}
