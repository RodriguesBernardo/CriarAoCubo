<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class CalendarioController extends Controller
{
    public function index()
    {
        return view('calendario.index');
    }

    public function getEvents(Request $request)
    {
        $query = Calendario::where('user_id', Auth::id())
            ->whereBetween('inicio', [$request->start, $request->end]);

        // FILTRO: Se vier ?pessoas=Bernardo,Gabriele
        if ($request->has('pessoas')) {
            $pessoas = explode(',', $request->pessoas);
            
            $query->where(function($q) use ($pessoas) {
                foreach ($pessoas as $pessoa) {
                    $q->orWhereJsonContains('participantes', $pessoa);
                }
            });
        }

        $eventos = $query->get();

        return response()->json($eventos->map(function ($evento) {
            $iniciais = '';
            // Verifica se existe e é um array antes de tentar map
            if(!empty($evento->participantes) && is_array($evento->participantes)) {
                $iniciais = '[' . implode(', ', array_map(fn($p) => substr($p, 0, 1), $evento->participantes)) . '] ';
            }

            return [
                'id' => $evento->id,
                'title' => $iniciais . $evento->titulo,
                'start' => $evento->inicio,
                'end'   => $evento->fim,
                'backgroundColor' => $evento->cor,
                'borderColor' => $evento->cor,
                'extendedProps' => [
                    'descricao' => $evento->descricao,
                    'participantes' => $evento->participantes, 
                    'grupo_id' => $evento->grupo_id
                ]
            ];
        }));
    }

    public function store(Request $request)
    {
        // 1. Validação Correta
        $dados = $request->validate([
            'titulo' => 'required|string',
            'inicio' => 'required|date',
            'fim'    => 'required|date|after_or_equal:inicio',
            'cor'    => 'required',
            'participantes' => 'required|array', // Valida o array
            'descricao' => 'nullable',
            'recorrencia' => 'nullable|in:nenhuma,diaria,semanal,mensal',
            'recorrencia_fim' => 'nullable|date|required_if:recorrencia,diaria,semanal,mensal'
        ]);

        $grupoId = Str::uuid();     
        
        // 2. Criação do evento base (CORRIGIDO: 'participantes' em vez de 'pessoa')
        $eventoBase = Calendario::create([
            'user_id' => Auth::id(),
            'titulo' => $dados['titulo'],
            'inicio' => $dados['inicio'],
            'fim' => $dados['fim'],
            'cor' => $dados['cor'],
            'participantes' => $dados['participantes'], // <-- AQUI MUDOU
            'descricao' => $dados['descricao'],
            'grupo_id' => $grupoId
        ]);

        // 3. Lógica de Recorrência
        if ($request->recorrencia && $request->recorrencia !== 'nenhuma') {
            $inicioAtual = Carbon::parse($dados['inicio']);
            $fimAtual    = Carbon::parse($dados['fim']);
            $limite      = Carbon::parse($request->recorrencia_fim)->endOfDay();
            $diffMinutos = $inicioAtual->diffInMinutes($fimAtual);

            while (true) {
                if ($request->recorrencia === 'diaria') {
                    $inicioAtual->addDay();
                } elseif ($request->recorrencia === 'semanal') {
                    $inicioAtual->addWeek();
                } elseif ($request->recorrencia === 'mensal') {
                    $inicioAtual->addMonth();
                }

                if ($inicioAtual->gt($limite)) {
                    break;
                }

                $novoFim = $inicioAtual->copy()->addMinutes($diffMinutos);

                // 4. Criação da cópia (CORRIGIDO: 'participantes' em vez de 'pessoa')
                Calendario::create([
                    'user_id' => Auth::id(),
                    'titulo' => $dados['titulo'],
                    'inicio' => $inicioAtual,
                    'fim' => $novoFim,
                    'cor' => $dados['cor'],
                    'participantes' => $dados['participantes'], // <-- AQUI MUDOU
                    'descricao' => $dados['descricao'],
                    'grupo_id' => $grupoId
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $evento = Calendario::where('user_id', Auth::id())->findOrFail($id);        
        
        // 5. Update (CORRIGIDO: removeu 'pessoa' e adicionou 'participantes')
        $evento->update($request->only([
            'titulo', 
            'inicio', 
            'fim', 
            'cor', 
            'descricao', 
            'participantes' // <-- AQUI MUDOU
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $evento = Calendario::where('user_id', Auth::id())->findOrFail($id);
        $evento->delete();
        
        return response()->json(['success' => true]);
    }
}