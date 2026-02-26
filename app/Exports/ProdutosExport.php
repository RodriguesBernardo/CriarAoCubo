<?php

namespace App\Exports;

use App\Models\Produto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdutosExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Produto::when($this->search, function($query) {
                return $query->where('nome', 'like', "%{$this->search}%")
                           ->orWhere('descricao', 'like', "%{$this->search}%");
            })
            ->get()
            ->map(function ($produto) {
                return [
                    'ID' => $produto->id,
                    'Nome' => $produto->nome,
                    'Descrição' => $produto->descricao,
                    'Preço' => 'R$ ' . number_format($produto->preco, 2, ',', '.'),
                    'Quantidade' => $produto->quantidade,
                    'Tempo de Impressão' => $produto->tempo_impressao,
                    'Criado em' => $produto->created_at->format('d/m/Y H:i'),
                    'Atualizado em' => $produto->updated_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Descrição',
            'Preço',
            'Quantidade',
            'Tempo de Impressão',
            'Criado em',
            'Atualizado em'
        ];
    }
}