<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'nome_original', 'caminho', 'tipo', 'tamanho'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}