<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiasFechados extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'horario_inicio',
        'horario_fim',
        'tipo',
        'observacao',
    ];

    protected $casts = [
        'data' => 'date',
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
    ];
}
