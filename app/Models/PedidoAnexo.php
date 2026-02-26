<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoAnexo extends Model
{
    protected $fillable = [
        'pedido_id',
        'nome_original',
        'caminho',
        'mime_type',
        'tamanho'
    ];
    
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}