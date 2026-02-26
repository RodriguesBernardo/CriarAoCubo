<?php

namespace App\Exports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientesExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Cliente::when($this->search, function($query) {
            return $query->where('nome', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('telefone', 'like', "%{$this->search}%")
                        ->orWhere('cnpj_cpf', 'like', "%{$this->search}%");
        })
        ->get()
        ->map(function ($cliente) {
            return [
                'Nome' => $cliente->nome,
                'Email' => $cliente->email,
                'Telefone' => $cliente->telefone,
                'CPF/CNPJ' => $cliente->cnpj_cpf,
                'Endereço' => $cliente->endereco,
                'Data Cadastro' => $cliente->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Email',
            'Telefone',
            'CPF/CNPJ',
            'Endereço',
            'Data Cadastro'
        ];
    }
}