<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'data_pedido',
        'data_entrega_prevista',
        'dias_entrega', // Adicionei este campo que estava faltando
        'status',
        'observacoes',
        'pago',
        'desconto',
        'valor_total',
        // Campos dos arquivos
        'comprovante_path',
        'comprovante_mime_type',
        'comprovante_original_name',
        'comprovante_size',
        'contrato_path',
        'contrato_mime_type',
        'contrato_original_name',
        'contrato_size',
        'outros_arquivos_path',
        'outros_arquivos_mime_type',
        'outros_arquivos_original_name',
        'outros_arquivos_size',
        'arquivado'
    ];

    protected $casts = [
        'data_pedido' => 'datetime', // Alterado para datetime para incluir horas
        'data_entrega_prevista' => 'datetime',
        'pago' => 'boolean', // Adicionado cast para boolean
        'arquivado' => 'boolean'
    ];

    protected $appends = [
        'comprovante_url',
        'contrato_url',
        'outros_arquivos_url'
    ];

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'item_pedido')
            ->withPivot([
                'id',
                'quantidade', 
                'preco_unitario', 
                'desconto',
                'tempo_estimado', 
                'peso_estimado', 
                'custo_estimado', 
                'observacoes',
                'pronto'
            ])
            ->withTimestamps();
    }
    

    public function arquivos()
    {
        return $this->hasMany(PedidoAnexo::class);
    }

    // Métodos para acessar os arquivos
    public function getComprovanteUrlAttribute()
    {
        return $this->comprovante_path ? Storage::url($this->comprovante_path) : null;
    }

    public function getContratoUrlAttribute()
    {
        return $this->contrato_path ? Storage::url($this->contrato_path) : null;
    }

    public function getOutrosArquivosUrlAttribute()
    {
        return $this->outros_arquivos_path ? Storage::url($this->outros_arquivos_path) : null;
    }

    public function getTamanhoFormatadoAttribute($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // Escopos para status
    public function scopeOrcamento($query)
    {
        return $query->where('status', 'orcamento');
    }

    public function scopeAberto($query)
    {
        return $query->where('status', 'aberto');
    }

    public function scopeEmProducao($query)
    {
        return $query->where('status', 'em_producao');
    }

    public function scopeFinalizado($query)
    {
        return $query->where('status', 'finalizado');
    }

    public function scopeEntregue($query)
    {
        return $query->where('status', 'entregue');
    }

    // Método para calcular o valor total
    public function calcularValorTotal()
    {
        $total = $this->produtos->sum(function($produto) {
            return ($produto->pivot->preco_unitario * $produto->pivot->quantidade) - ($produto->pivot->desconto ?? 0);
        });

        $this->valor_total = max(0, $total - ($this->desconto ?? 0));
        return $this->valor_total;
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class);
    }
}