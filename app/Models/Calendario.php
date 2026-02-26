<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    use HasFactory;
    protected $table = 'calendario';

    protected $fillable = [
        'user_id',
        'titulo',
        'inicio',
        'fim',
        'cor',
        'descricao',
        'participantes',
        'grupo_id'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'participantes' => 'array', 
    ];
}
