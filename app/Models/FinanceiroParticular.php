<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceiroParticular extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'financeiros_particulares';

    protected $fillable = [
        'user_id', 'descricao', 'valor', 'data_vencimento', 
        'tipo', 'pago', 'categoria', 'responsavel',
        'is_fixo', 'is_parcelado', 'parcela_atual', 
        'total_parcelas', 'grupo_id'
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'pago' => 'boolean',
        'is_fixo' => 'boolean',
        'is_parcelado' => 'boolean'
    ];
}