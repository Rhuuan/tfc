<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoFerramenta extends Model
{
    protected $fillable = [
        'nome',
        'tipo',
        'descricao',
    ];
}
