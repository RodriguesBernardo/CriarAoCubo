<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'arquivos' => 'required|array',
            'arquivos.*' => 'file|mimes:stl|max:10240', // 10MB max
        ]);
        
        $pedido = Pedido::find($request->pedido_id);
        
        foreach ($request->file('arquivos') as $arquivo) {
            $path = $arquivo->store('uploads', 'uploads');
            
            Arquivo::create([
                'pedido_id' => $pedido->id,
                'nome_original' => $arquivo->getClientOriginalName(),
                'caminho' => $path,
                'tipo' => $arquivo->getClientMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }
        
        return back()->with('success', 'Arquivos enviados com sucesso!');
    }

    public function destroy(Arquivo $arquivo)
    {
        Storage::disk('uploads')->delete($arquivo->caminho);
        $arquivo->delete();
        
        return back()->with('success', 'Arquivo removido com sucesso!');
    }
}