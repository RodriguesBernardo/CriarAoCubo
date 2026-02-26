<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    protected $table = 'item_pedido';

    protected $fillable = [
        'pedido_id', 
        'produto_id', 
        'quantidade', 
        'preco_unitario',
        'desconto', // Adicionado para consistência com o formulário
        'tempo_estimado', 
        'peso_estimado', 
        'custo_estimado', 
        'observacoes'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'desconto' => 'decimal:2',
        'tempo_estimado' => 'decimal:2',
        'peso_estimado' => 'decimal:2',
        'custo_estimado' => 'decimal:2',    
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    // Calcula o subtotal do item (preço * quantidade - desconto)
    public function getSubtotalAttribute()
    {
        return ($this->preco_unitario * $this->quantidade) - ($this->desconto ?? 0);
    }

    // Calcula o custo total estimado
    public function getCustoTotalAttribute()
    {
        return $this->custo_estimado * $this->quantidade;
    }

    // Calcula a margem de lucro do item
    public function getMargemLucroAttribute()
    {
        if ($this->custo_estimado == 0) return 0;
        
        return (($this->preco_unitario - $this->custo_estimado) / $this->custo_estimado) * 100;
    }
}