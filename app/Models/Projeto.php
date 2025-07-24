<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function fases()
    {
        return $this->belongsToMany(Fase::class);
    }

    public function atividades()
    {
        return $this->belongsToMany(Atividade::class);
    }

    public function tarefas()
    {
        return $this->belongsToMany(Tarefa::class);
    }

    public function metodoFerramentas()
    {
        return $this->belongsToMany(MetodoFerramenta::class);
    }
}
