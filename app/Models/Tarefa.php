<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    public function metodosFerramentas()
    {
        return $this->belongsToMany(MetodoFerramenta::class, 'metodo_ferramenta_tarefa');
    }

    protected $fillable = [
        'nome',
        'descricao',
    ];
}
