<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdutosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $produtos = Produto::when($search, function ($query) use ($search) {
                return $query->where('nome', 'like', '%'.$search.'%');
            })
            ->orderBy('nome')
            ->paginate(10);
        
        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'preco_custo' => 'required|numeric|min:0',
            'custo_estimado_por_hora' => 'required|numeric|min:0',
            'custo_estimado_por_grama' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'custo_estimado_energia' => 'required|numeric|min:0',
            'tempo_impressao' => 'required|date_format:H:i:s',
            'imagem' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'arquivo_stl' => [
                'file',
                'max:1048576',
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['stl', 'obj', '3ds', 'zip', 'rar', '3mf'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail("O arquivo deve ser do tipo: " . implode(', ', $allowedExtensions));
                    }
                },
            ],
        ]);

        try {
            $produto = new Produto();
            $produto->nome = $request->nome;
            $produto->descricao = $request->descricao;
            $produto->preco = $request->preco;
            $produto->preco_custo = $request->preco_custo;
            $produto->custo_estimado_por_hora = $request->custo_estimado_por_hora;
            $produto->custo_estimado_por_grama = $request->custo_estimado_por_grama;
            $produto->custo_estimado_energia = $request->custo_estimado_energia;
            $produto->quantidade = $request->quantidade;
            $produto->tempo_impressao = $request->tempo_impressao;

            // Upload da imagem
            if ($request->hasFile('imagem')) {
                $imagemPath = $request->file('imagem')->store('produtos/imagens', 'public');
                $produto->imagem = $imagemPath;
            }

            // Upload do arquivo 3D mantendo nome original
            if ($request->hasFile('arquivo_stl')) {
                $arquivo = $request->file('arquivo_stl');
                $nomeOriginal = $arquivo->getClientOriginalName();
                $stlPath = $arquivo->storeAs('produtos/stl', $nomeOriginal, 'public');
                $produto->arquivo_stl = $stlPath;
            }

            $produto->save();

            return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar produto: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $produto = Produto::findOrFail($id);
        return view('produtos.show', compact('produto'));
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        
        $results = Produto::where('nome', 'like', '%'.$term.'%')
            ->select('id', 'nome as text', 'preco')
            ->paginate(10);
        
        $formattedResults = $results->map(function($item) {
            return [
                'id' => $item->id,
                'text' => $item->text . ' (R$ ' . number_format($item->preco, 2, ',', '.') . ')',
                'preco' => $item->preco
            ];
        });
        
        return response()->json([
            'data' => $formattedResults,
            'total' => $results->total()
        ]);
    }

    public function edit($id)
    {
        $produto = Produto::findOrFail($id);
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'preco_custo' => 'required|numeric|min:0',
            'custo_estimado_por_hora' => 'required|numeric|min:0',
            'custo_estimado_por_grama' => 'required|numeric|min:0',
            'custo_estimado_energia' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'tempo_impressao' => 'required|date_format:H:i:s',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'arquivo_stl' => [
                'nullable',
                'file',
                'max:1048576',
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['stl', 'obj', '3ds', 'zip', 'rar', '3mf'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail("O arquivo deve ser do tipo: " . implode(', ', $allowedExtensions));
                    }
                },
            ],
        ]);

        try {
            $produto = Produto::findOrFail($id);
            $produto->nome = $request->nome;
            $produto->descricao = $request->descricao;
            $produto->preco = $request->preco;
            $produto->preco_custo = $request->preco_custo;
            $produto->custo_estimado_por_hora = $request->custo_estimado_por_hora;
            $produto->custo_estimado_por_grama = $request->custo_estimado_por_grama;
            $produto->custo_estimado_energia = $request->custo_estimado_energia;
            $produto->quantidade = $request->quantidade;
            $produto->tempo_impressao = $request->tempo_impressao;

            // Atualizar imagem se fornecida
            if ($request->hasFile('imagem')) {
                if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
                    Storage::disk('public')->delete($produto->imagem);
                }
                
                $imagemPath = $request->file('imagem')->store('produtos/imagens', 'public');
                $produto->imagem = $imagemPath;
            }

            // Atualizar arquivo 3D se fornecido (mantendo nome original)
            if ($request->hasFile('arquivo_stl')) {
                if ($produto->arquivo_stl && Storage::disk('public')->exists($produto->arquivo_stl)) {
                    Storage::disk('public')->delete($produto->arquivo_stl);
                }
                
                $arquivo = $request->file('arquivo_stl');
                $nomeOriginal = $arquivo->getClientOriginalName();
                $stlPath = $arquivo->storeAs('produtos/stl', $nomeOriginal, 'public');
                $produto->arquivo_stl = $stlPath;
            }

            $produto->save();

            return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar produto: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $produto = Produto::findOrFail($id);
            
            if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
                Storage::disk('public')->delete($produto->imagem);
            }
            
            if ($produto->arquivo_stl && Storage::disk('public')->exists($produto->arquivo_stl)) {
                Storage::disk('public')->delete($produto->arquivo_stl);
            }
            
            $produto->delete();
            
            return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('produtos.index')->with('error', 'Erro ao excluir produto: '.$e->getMessage());
        }
    }

    public function downloadStl($id)
    {
        $produto = Produto::findOrFail($id);
        
        $path = storage_path('app/public/'.$produto->arquivo_stl);
        $originalName = basename($produto->arquivo_stl);
        
        // Mapeamento de tipos MIME
        $mimeTypes = [
            'stl' => 'application/sla',
            'obj' => 'application/obj',
            '3ds' => 'application/x-3ds',
            '3mf' => 'application/3mf',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed'
        ];
        
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        return response()->download($path, $originalName, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$originalName.'"',
        ]);
    }

    public function export($type)
    {
        $search = request()->query('search');

        if ($type === 'pdf') {
            $produtos = Produto::when($search, function($query) use ($search) {
                    return $query->where('nome', 'like', "%{$search}%")
                            ->orWhere('descricao', 'like', "%{$search}%");
                })
                ->get();

            $pdf = Pdf::loadView('produtos.pdf', compact('produtos', 'search'));
            return $pdf->download('produtos.pdf');
        }

        return Excel::download(new ProdutosExport($search), 'produtos.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}