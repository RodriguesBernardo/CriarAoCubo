<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\ClientesExport;    
use Barryvdh\DomPDF\Facade\Pdf;      

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        
        $clientes = Cliente::when($search, function($query) use ($search) {
                return $query->where('nome', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('telefone', 'like', "%{$search}%")
                             ->orWhere('cnpj_cpf', 'like', "%{$search}%");
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();
            
        return view('clientes.index', compact('clientes', 'search'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefone' => 'required|string|max:20|regex:/^\(\d{2}\) \d{4,5}-\d{4}$/',
            'cnpj_cpf' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $cleaned = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($cleaned) === 11 && !$this->validaCPF($cleaned)) {
                        $fail('CPF inválido.');
                    } elseif (strlen($cleaned) === 13 && !$this->validaCNPJ($cleaned)) {
                        $fail('CNPJ inválido.');
                    } elseif (strlen($cleaned) > 0 && !in_array(strlen($cleaned), [11, 14])) {
                        $fail('Documento inválido.');
                    }
                }
            ],
            'endereco' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['telefone'] = preg_replace('/[^0-9]/', '', $data['telefone']);
        $data['cnpj_cpf'] = preg_replace('/[^0-9]/', '', $data['cnpj_cpf']);

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $totalPedidos = $cliente->pedidos()->count();
        return view('clientes.show', compact('cliente', 'totalPedidos'));
    }   

    public function edit(Cliente $cliente)
    {
        // Formatar os valores para exibição
        $cliente->telefone = $this->formatPhone($cliente->telefone);
        $cliente->cnpj_cpf = $this->formatDocument($cliente->cnpj_cpf);
        
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,'.$cliente->id,
            'telefone' => 'required|string|max:20|regex:/^\(\d{2}\) \d{4,5}-\d{4}$/',
            'cnpj_cpf' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $cleaned = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($cleaned) === 11 && !$this->validaCPF($cleaned)) {
                        $fail('CPF inválido.');
                    } elseif (strlen($cleaned) === 14 && !$this->validaCNPJ($cleaned)) {
                        $fail('CNPJ inválido.');
                    } elseif (strlen($cleaned) > 0 && !in_array(strlen($cleaned), [11, 14])) {
                        $fail('Documento inválido.');
                    }
                }
            ],
            'endereco' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                /* ->withErrors($validator) */
                ->withInput();
        }

        $data = $request->all();
        $data['telefone'] = preg_replace('/[^0-9]/', '', $data['telefone']);
        $data['cnpj_cpf'] = preg_replace('/[^0-9]/', '', $data['cnpj_cpf']);

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    public function search(Request $request)
    {
    $term = $request->input('q');
    
    $results = Cliente::where('nome', 'like', '%'.$term.'%')
        ->select('id', 'nome as text')
        ->paginate(10);
    
    return response()->json([
        'data' => $results->items(),
        'total' => $results->total()
    ]);
    }

    // Métodos auxiliares
    private function validaCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    private function validaCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        if ($cnpj[12] != $digito1) {
            return false;
        }
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        if ($cnpj[13] != $digito2) {
            return false;
        }
        return true;
    }

    private function formatPhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        return $phone;
    }

    private function formatDocument($document) {
        $document = preg_replace('/[^0-9]/', '', $document);
        if (strlen($document) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document);
        } elseif (strlen($document) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document);
        }
        return $document;
    }

    public function export($type)
    {
        $search = request()->query('search');
        
        if ($type === 'pdf') {
            $clientes = Cliente::when($search, function($query) use ($search) {
                return $query->where('nome', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('telefone', 'like', "%{$search}%")
                            ->orWhere('cnpj_cpf', 'like', "%{$search}%");
            })->get();
            
            $pdf = PDF::loadView('clientes.pdf', compact('clientes', 'search'));
            return $pdf->download('clientes.pdf');
        }
        
        return Excel::download(new ClientesExport($search), 'clientes.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}