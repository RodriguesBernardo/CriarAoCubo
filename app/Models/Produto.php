<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'preco_custo',
        'custo_estimado_por_hora',
        'custo_estimado_por_grama',
        'quantidade',
        'imagem',
        'arquivo_stl'
    ];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'item_pedido')
            ->withPivot('quantidade', 'preco_unitario', 'tempo_estimado', 'peso_estimado', 'custo_estimado', 'observacoes')
            ->withTimestamps();
    }
}