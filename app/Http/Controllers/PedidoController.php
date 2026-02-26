<?php
namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Financeiro;
use App\Models\PedidoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PedidosExport;
use App\Models\PedidoAnexo;


class PedidoController extends Controller
{
    // Lista todos os pedidos
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $pedidos = Pedido::with('cliente')
            ->when($search, function($query) use ($search) {
                return $query->where('id', $search)
                    ->orWhereHas('cliente', function($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%");
                    });
            })
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalPedidos = Pedido::count();
            
        // Carrega os pedidos pendentes, em produção, finalizados e entregues
        $orcamento = Pedido::with('cliente')
            ->where('status', 'orcamento')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        
        $pendentes = Pedido::with('cliente')
            ->whereNotIn('status', ['finalizado', 'entregue', 'em_producao', 'orcamento'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $em_producao = Pedido::with('cliente')
            ->where('status', 'em_producao')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $finalizado = Pedido::with('cliente')
            ->where('status', 'finalizado')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();    

        $entregue = Pedido::with('cliente')
            ->where('status', 'entregue')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $finalizadosNaoPagos = Pedido::with('cliente')
            ->whereIn('status', ['finalizado', 'entregue'])
            ->where('pago', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $pedidosProximos = Pedido::whereNotNull('data_entrega_prevista')
            ->whereBetween('data_entrega_prevista', [
                now()->subDays(1), // Inclui pedidos com 1 dia de atraso
                now()->addDays(3)   // Mostra pedidos com até 3 dias de antecedência
            ])
            ->whereIn('status', ['aberto', 'em_producao']) // Apenas pedidos não finalizados
            ->orderBy('data_entrega_prevista')
            ->with('cliente')
            ->get();

        return view('pedidos.index', compact('pedidos', 'totalPedidos', 'pendentes', 'em_producao', 'finalizado', 'entregue', 'orcamento', 'search', 'status', 'finalizadosNaoPagos', 'pedidosProximos'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();
        return view('pedidos.create', compact('clientes', 'produtos'));
    }

    // Armazena um novo pedido
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'produtos' => 'required|array|min:1',
            'produtos.*.produto_id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|numeric|min:1',
            'dias_entrega' => 'nullable|numeric|min:1',
            'produtos.*.preco_unitario' => 'required|numeric|min:0',
            'produtos.*.desconto' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:500',
            'desconto' => 'nullable|numeric|min:0',
            'data_entrega_prevista' => 'nullable|date',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:2048',
            'contrato' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:2048',
            'outros_arquivos' => 'nullable|file|mimes:pdf,jpg,png,doc,docx,zip,rar|max:5120',
            'arquivos.*' => 'nullable|file|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $pedido = Pedido::create([
                'cliente_id' => $request->cliente_id,
                'data_pedido' => now(),
                'data_entrega_prevista' => $request->data_entrega_prevista,
                'status' => $request->status ?? 'aberto', 
                'observacoes' => $request->observacoes,
                'valor_total' => 0,
                'desconto' => $request->desconto ?? 0,
                'pago' => $request->has('pago'),
            ]);

            $valorTotal = 0;

            foreach ($request->produtos as $produto) {
                $produtoModel = Produto::findOrFail($produto['produto_id']); // Corrigido aqui
                
                $custoEstimado = 0;
                if (!empty($produto['tempo_estimado'])) {
                    $custoEstimado += $produtoModel->custo_estimado_por_hora * $produto['tempo_estimado'];
                }
                if (!empty($produto['peso_estimado'])) {
                    $custoEstimado += $produtoModel->custo_estimado_por_grama * $produto['peso_estimado'];
                }

                $subtotal = ($produto['preco_unitario'] * $produto['quantidade']) - ($produto['desconto'] ?? 0);
                $valorTotal += $subtotal;

                $pedido->produtos()->attach($produtoModel->id, [
                    'quantidade' => $produto['quantidade'],
                    'preco_unitario' => $produto['preco_unitario'],
                    'desconto' => $produto['desconto'] ?? 0,
                    'tempo_estimado' => $produto['tempo_estimado'] ?? null,
                    'peso_estimado' => $produto['peso_estimado'] ?? null,
                    'custo_estimado' => $custoEstimado,
                    'observacoes' => $produto['observacoes'] ?? null,
                ]);
            }

            // Aplica desconto geral
            $valorTotal -= $request->desconto ?? 0;
            if ($valorTotal < 0) $valorTotal = 0;

            // Atualiza o valor total do pedido
            $pedido->update(['valor_total' => $valorTotal]);

            // Processa arquivos específicos (comprovante, contrato, outros)
            if ($request->hasFile('comprovante')) {
                $this->processarArquivo($request->file('comprovante'), $pedido, 'comprovante');
            }

            if ($request->hasFile('contrato')) {
                $this->processarArquivo($request->file('contrato'), $pedido, 'contrato');
            }

            if ($request->hasFile('outros_arquivos')) {
                $this->processarArquivo($request->file('outros_arquivos'), $pedido, 'outros_arquivos');
            }

            if (!$request->has('data_entrega_prevista') && $request->has('dias_entrega')) {
                $dataEntrega = now()->addDays($request->dias_entrega);
                $pedido->update(['data_entrega_prevista' => $dataEntrega]);
            }

            // Processa arquivos anexados adicionais
            if ($request->hasFile('arquivos')) {
                foreach ($request->file('arquivos') as $arquivo) {
                    try {
                        $nomeOriginal = $arquivo->getClientOriginalName();
                        $path = $arquivo->store("pedidos/{$pedido->id}", 'public');
                        
                        $pedido->anexos()->create([
                            'nome_original' => $nomeOriginal,
                            'caminho' => $path,
                            'mime_type' => $arquivo->getMimeType(),
                            'tamanho' => $arquivo->getSize()
                        ]);
            
                    } catch (\Exception $e) {
                        \Log::error("Erro ao processar arquivo {$nomeOriginal}: " . $e->getMessage());
                        continue;
                    }
                }
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', 'Pedido criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro ao criar pedido: " . $e->getMessage());
            return back()->with('error', 'Erro ao criar pedido: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    // Mostra um pedido específico
    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'produtos', 'arquivos');
        return view('pedidos.show', compact('pedido'));
    }

    // Mostra o formulário de edição
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();
        
        // Carrega os produtos do pedido com os dados do pivot
        $pedido->load(['produtos' => function($query) {
            $query->withPivot([
                'quantidade', 
                'preco_unitario', 
                'observacoes',
                'tempo_estimado',
                'peso_estimado',
                'custo_estimado'
            ]);
        }]);
        
        return view('pedidos.edit', compact('pedido', 'clientes', 'produtos'));
    }

    // Atualiza um pedido existente
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'status' => 'required|in:orcamento,aberto,em_producao,finalizado,entregue',
            'produtos' => 'required|array|min:1',
            'produtos.*.produto_id' => 'required|exists:produtos,id', // Corrigido para produto_id
            'produtos.*.quantidade' => 'required|numeric|min:1',
            'produtos.*.preco_unitario' => 'required|numeric|min:0',
            'dias_entrega' => 'nullable|numeric|min:1',
            'produtos.*.desconto' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:500',
            'desconto' => 'nullable|numeric|min:0',
            'pago' => 'sometimes|boolean',
            'arquivos.*' => 'nullable|file|max:2048',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,png,zip,rar|max:2048',
            'contrato' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:2048',
            'outros_arquivos' => 'nullable|file|mimes:pdf,jpg,png,doc,docx,zip,rar|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Atualiza informações básicas do pedido
            $pedido->update([
                'cliente_id' => $request->cliente_id,
                'status' => $request->status,
                'observacoes' => $request->observacoes,
                'desconto' => $request->desconto ?? 0,
                'pago' => $request->boolean('pago'),
                'data_entrega_prevista' => $request->data_entrega_prevista,
                'dias_entrega' => $request->dias_entrega,
            ]);

            // Processa os produtos do pedido
            $valorTotal = 0;
            $produtosAtualizados = [];

            foreach ($request->produtos as $produtoData) {
                // Verifica se o produto está marcado para remoção
                if (isset($produtoData['_remover']) && $produtoData['_remover'] == '1') {
                    continue;
                }

                $produtoModel = Produto::findOrFail($produtoData['produto_id']);
                
                // Calcula custo estimado (se aplicável)
                $custoEstimado = 0;
                if (!empty($produtoData['tempo_estimado'])) {
                    $custoEstimado += $produtoModel->custo_estimado_por_hora * $produtoData['tempo_estimado'];
                }
                if (!empty($produtoData['peso_estimado'])) {
                    $custoEstimado += $produtoModel->custo_estimado_por_grama * $produtoData['peso_estimado'];
                }

                // Calcula subtotal
                $subtotal = ($produtoData['preco_unitario'] * $produtoData['quantidade']) - ($produtoData['desconto'] ?? 0);
                $valorTotal += $subtotal;

                // Se tiver pivot_id, atualiza o registro existente
                if (isset($produtoData['pivot_id'])) {
                    DB::table('item_pedido')
                        ->where('id', $produtoData['pivot_id'])
                        ->update([
                            'quantidade' => $produtoData['quantidade'],
                            'preco_unitario' => $produtoData['preco_unitario'],
                            'desconto' => $produtoData['desconto'] ?? 0,
                            'tempo_estimado' => $produtoData['tempo_estimado'] ?? null,
                            'peso_estimado' => $produtoData['peso_estimado'] ?? null,
                            'custo_estimado' => $custoEstimado,
                            'observacoes' => $produtoData['observacoes'] ?? null,
                        ]);
                    
                    $produtosAtualizados[] = $produtoData['pivot_id'];
                } else {
                    // Adiciona novo produto
                    $pedido->produtos()->attach($produtoData['produto_id'], [
                        'quantidade' => $produtoData['quantidade'],
                        'preco_unitario' => $produtoData['preco_unitario'],
                        'desconto' => $produtoData['desconto'] ?? 0,
                        'tempo_estimado' => $produtoData['tempo_estimado'] ?? null,
                        'peso_estimado' => $produtoData['peso_estimado'] ?? null,
                        'custo_estimado' => $custoEstimado,
                        'observacoes' => $produtoData['observacoes'] ?? null,
                    ]);
                }
            }

            // Remove produtos que não estão mais na lista
            $pedido->produtos()->wherePivotNotIn('id', $produtosAtualizados)->detach();

            // Aplica desconto geral e atualiza valor total
            $valorTotal -= $request->desconto ?? 0;
            $valorTotal = max(0, $valorTotal);
            
            $pedido->update(['valor_total' => $valorTotal]);

            // Processa arquivos anexados
            $this->processarArquivos($request, $pedido);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', 'Pedido atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro ao atualizar pedido #{$pedido->id}: " . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function processarArquivos(Request $request, Pedido $pedido)
    {
        // Processa arquivos específicos (comprovante, contrato, outros)
        if ($request->hasFile('comprovante')) {
            $this->processarArquivo($request->file('comprovante'), $pedido, 'comprovante');
        }

        if ($request->hasFile('contrato')) {
            $this->processarArquivo($request->file('contrato'), $pedido, 'contrato');
        }

        if ($request->hasFile('outros_arquivos')) {
            $this->processarArquivo($request->file('outros_arquivos'), $pedido, 'outros_arquivos');
        }

        // Processa novos arquivos anexados
        if ($request->hasFile('arquivos')) {
            foreach ($request->file('arquivos') as $arquivo) {
                try {
                    $nomeOriginal = $arquivo->getClientOriginalName();
                    $path = $arquivo->store("pedidos/{$pedido->id}", 'public');
                    
                    $pedido->anexos()->create([
                        'nome_original' => $nomeOriginal,
                        'caminho' => $path,
                        'mime_type' => $arquivo->getMimeType(),
                        'tamanho' => $arquivo->getSize()
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Erro ao processar arquivo {$nomeOriginal}: " . $e->getMessage());
                    continue;
                }
            }
        }
    }

    public function destroy(Pedido $pedido)
    {
        \Log::info("Tentativa de excluir pedido #{$pedido->id}");
        
        try {
            DB::beginTransaction();
            \Log::info("Transação iniciada para pedido #{$pedido->id}");

            \Log::info("Removendo produtos do pedido #{$pedido->id}");
            // Remove todos os itens do pedido
            $pedido->produtos()->detach();

            \Log::info("Removendo arquivos do pedido #{$pedido->id}");
            // Remove arquivos específicos
            $this->removerArquivosEspecificos($pedido);

            \Log::info("Removendo anexos do pedido #{$pedido->id}");
            // Remove anexos gerais - VERIFICAÇÃO SEGURA
            if ($pedido->anexos && $pedido->anexos->count() > 0) {
                foreach ($pedido->anexos as $anexo) {
                    Storage::disk('public')->delete($anexo->caminho);
                    $anexo->delete();
                }
            } else {
                \Log::info("Nenhum anexo encontrado para o pedido #{$pedido->id}");
            }

            \Log::info("Excluindo pedido #{$pedido->id}");
            // Finalmente exclui o pedido
            $pedido->delete();

            DB::commit();
            \Log::info("Pedido #{$pedido->id} excluído com sucesso");

            return redirect()->route('pedidos.index')
                ->with('success', 'Pedido excluído com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro ao excluir pedido #{$pedido->id}: " . $e->getMessage());
            return back()->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }
        
    private function removerArquivosEspecificos(Pedido $pedido)
    {
        $arquivos = [
            'comprovante_path',
            'contrato_path',
            'outros_arquivos_path'
        ];
    
        foreach ($arquivos as $arquivo) {
            if ($pedido->$arquivo && Storage::exists($pedido->$arquivo)) {
                Storage::delete($pedido->$arquivo);
            }
        }
    }
    // Atualiza apenas o status do pedido
    public function updateStatus(Request $request, Pedido $pedido)
    {
        $request->validate([
            'status' => 'required|in:orcamento,aberto,em_producao,finalizado,entregue',
            'pago' => 'sometimes|boolean',
        ]);
        
        $pedido->update([
            'status' => $request->status,
            'pago' => $request->has('pago') ? true : false,
        ]);
        
        return back()->with('success', 'Status atualizado com sucesso!');
    }

    // Remove um arquivo do pedido
    public function removeArquivo(Request $request, Pedido $pedido)
    {
        $request->validate([
            'arquivo_id' => 'required|exists:arquivos,id'
        ]);

        $arquivo = $pedido->anexos()->findOrFail($request->arquivo_id);
        
        Storage::delete($arquivo->caminho);
        $arquivo->delete();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }
    // Adicione este método ao seu PedidoController
    public function gerarPdf($id)
    {
        $pedido = Pedido::with('cliente', 'produtos')->findOrFail($id);
        
        $data = [
            'pedido' => $pedido,
            'empresa' => [
                'nome' => 'criar³',
                'cidade' => 'Bento Gonçalves - RS',
                'telefone' => '(54) 9 9194-5373',
                'email' => 'criaraocubo@gmail.com',
            ],
            'dataEmissao' => now()->format('d/m/Y'),
            'dataEntrega' => $pedido->dias_entrega ? now()->addDays($pedido->dias_entrega)->format('d/m/Y') : 'A combinar',
            'formaPagamento' => '50% no pedido e 50% na entrega'
        ];
        
        $pdf = Pdf::loadView('pdf.pedido', $data)
                 ->setPaper('a4')
                 ->setOption('defaultFont', 'Helvetica');
        
        return $pdf->download("Pedido {$pedido->cliente->nome}.pdf");
    }

    public function export($type)
    {
        $search = request()->query('search');
        $status = request()->query('status');

        if ($type === 'pdf') {
            $pedidos = Pedido::with(['cliente', 'produtos'])
                ->when($search, function($query) use ($search) {
                    return $query->where('id', 'like', "%{$search}%")
                        ->orWhereHas('cliente', function($q) use ($search) {
                            $q->where('nome', 'like', "%{$search}%");
                        });
                })
                ->when($status, function($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->get();

            $pdf = Pdf::loadView('pedidos.pdf', compact('pedidos', 'search', 'status'));
            return $pdf->download('pedidos.pdf');
        }

        return Excel::download(new PedidosExport($search, $status), 'pedidos.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function removerAnexo(PedidoAnexo $anexo)
    {
        Storage::disk('public')->delete($anexo->caminho);
        $anexo->delete();
        
        return back()->with('success', 'Anexo removido com sucesso!');
    }
    // Método para processar o arquivo
    private function processarArquivo($arquivo, Pedido $pedido, $tipo)
    {
        $path = $arquivo->store("pedidos/{$pedido->id}/{$tipo}", 'public');
        
        $pedido->update([
            "{$tipo}_path" => $path,
            "{$tipo}_mime_type" => $arquivo->getMimeType(),
            "{$tipo}_original_name" => $arquivo->getClientOriginalName(),
            "{$tipo}_size" => $arquivo->getSize(),
        ]);
    }

    public function downloadArquivo(Pedido $pedido, $tipo)
    {
        $path = $pedido->{$tipo . '_path'};
        $nomeOriginal = $pedido->{$tipo . '_original_name'};

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path, $nomeOriginal);
    }

    public function removerArquivo(Pedido $pedido, $tipo)
    {
        $path = $pedido->{$tipo . '_path'};

        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }

        $pedido->update([
            $tipo . '_path' => null,
            $tipo . '_mime_type' => null,
            $tipo . '_original_name' => null,
            $tipo . '_size' => null,
        ]);

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    public function gerarComprovanteEntrega($id)
    {
        $pedido = Pedido::with('cliente', 'produtos')->findOrFail($id);
        
        $data = [
            'pedido' => $pedido,
            'empresa' => [
                'nome' => 'criar³',
                'cidade' => 'Bento Gonçalves - RS',
                'telefone' => '(54) 9 9194-5373',
                'email' => 'criaraocubo@gmail.com',
            ],
            'dataEmissao' => now()->format('d/m/Y'),
            'dataEntrega' => now()->format('d/m/Y'),
            'responsavelEntrega' => 'Entregue por: ________________________',
            'responsavelRecebimento' => 'Recebido por: ________________________'
        ];
        
        $pdf = Pdf::loadView('pdf.comprovante-entrega', $data)
                ->setPaper('a4')
                ->setOption('defaultFont', 'Helvetica');
        
        return $pdf->download("Comprovante de Entrega {$pedido->cliente->nome}.pdf");
    }

    public function updateProdutoStatus(Request $request, $pedidoId, $produtoId)
    {
        $request->validate([
            'pronto' => 'sometimes|boolean'
        ]);

        DB::table('item_pedido')
            ->where('pedido_id', $pedidoId)
            ->where('produto_id', $produtoId)
            ->update([
                'pronto' => $request->boolean('pronto', false)
            ]);

        return back()->with('success', 'Status do produto atualizado!');
    }


    public function updateStatusPagamento(Request $request, Pedido $pedido)
    {
        $request->validate([
            'pago' => 'required|boolean'
        ]);
        
        $pedido->update([
            'pago' => $request->boolean('pago')
        ]);
        
        return back()->with('success', 'Status de pagamento atualizado com sucesso!');
    }
    // Método para alternar status via AJAX
public function updateStatusAjax(Request $request, Pedido $pedido)
{
    try {
        $request->validate([
            'status' => 'required|in:orcamento,aberto,em_producao,finalizado,entregue'
        ]);
        
        $pedido->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar status: ' . $e->getMessage()
        ], 500);
    }
}

    public function archive(Pedido $pedido)
    {
        try {
            // Alternar o status de arquivado
            $pedido->arquivado = !$pedido->arquivado;
            $pedido->save();

            return response()->json([
                'success' => true,
                'message' => $pedido->arquivado ? 'Pedido arquivado com sucesso!' : 'Pedido desarquivado com sucesso!',
                'pedido' => $pedido
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação: ' . $e->getMessage()
            ], 500);
        }
    }
}