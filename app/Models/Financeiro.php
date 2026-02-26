<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financeiro extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'financeiro';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'descricao',
        'valor',
        'data',
        'tipo',
        'categoria',
        'observacoes',
        'recorrente',
        'parcela_atual',
        'total_parcelas',
        'pago',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
        'pago' => 'boolean',
        'recorrente' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'data',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Scope para filtrar receitas
     */
    public function scopeReceitas($query)
    {
        return $query->where('tipo', 'receita');
    }

    /**
     * Scope para filtrar despesas
     */
    public function scopeDespesas($query)
    {
        return $query->where('tipo', 'despesa');
    }

    /**
     * Scope para filtrar lançamentos pagos
     */
    public function scopePagos($query)
    {
        return $query->where('pago', true);
    }

    /**
     * Scope para filtrar lançamentos pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('pago', false);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('data', [$inicio, $fim]);
    }

    /**
     * Relacionamento com usuário (opcional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Acessor para valor formatado
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    /**
     * Acessor para data formatada
     */
    public function getDataFormatadaAttribute()
    {
        return $this->data->format('d/m/Y');
    }

    /**
     * Mutator para garantir valor positivo
     */
    public function setValorAttribute($value)
    {
        $this->attributes['valor'] = abs($value);
    }

    /**
     * Mutator para padronizar categoria
     */
    public function setCategoriaAttribute($value)
    {
        $this->attributes['categoria'] = ucfirst(strtolower($value));
    }
}