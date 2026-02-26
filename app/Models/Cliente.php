<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'email', 'telefone', 'endereco', 'cnpj_cpf', 'observacoes'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
    public function getTelefoneFormatadoAttribute()
    {
        $telefone = preg_replace('/[^0-9]/', '', $this->telefone);
        if(strlen($telefone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        }
        return $this->telefone;
    }

    public function getDocumentoFormatadoAttribute()
    {
        $documento = preg_replace('/[^0-9]/', '', $this->cnpj_cpf);
        if(strlen($documento) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $documento);
        } elseif(strlen($documento) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $documento);
        }
        return $this->cnpj_cpf;
    }
}