<?php

namespace App\Exports;

use App\Models\Pedido;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PedidosExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $status;

    public function __construct($search = null, $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    public function collection()
    {
        return Pedido::with(['cliente', 'produtos'])
            ->when($this->search, function($query) {
                return $query->where('id', 'like', "%{$this->search}%")
                    ->orWhereHas('cliente', function($q) {
                        $q->where('nome', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->status, function($query) {
                return $query->where('status', $this->status);
            })
            ->get()
            ->map(function ($pedido) {
                return [
                    'Número' => $pedido->id,
                    'Cliente' => $pedido->cliente->nome,
                    'Data' => $pedido->created_at->format('d/m/Y'),
                    'Entrega' => $pedido->data_entrega_prevista ? $pedido->data_entrega_prevista->format('d/m/Y') : 'N/D',
                    'Status' => ucfirst(str_replace('_', ' ', $pedido->status)),
                    'Valor Total' => number_format($pedido->valor_total, 2, ',', '.'),
                    'Pago' => $pedido->pago ? 'Sim' : 'Não',
                    'Itens' => $pedido->produtos->count(),
                    'Quantidade Total' => $pedido->produtos->sum('pivot.quantidade'),
                    'Observações' => $pedido->observacoes,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Número',
            'Cliente',
            'Data',
            'Entrega',
            'Status',
            'Valor Total',
            'Pago',
            'Itens',
            'Quantidade Total',
            'Observações'
        ];
    }
}