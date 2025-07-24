<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Projeto extends Model
{
    protected $fillable = ['nome', 'descricao'];

    public function fases(): BelongsToMany
    {
        return $this->belongsToMany(Fase::class, 'fase_projeto');
    }

    public function atividades(): BelongsToMany
    {
        return $this->belongsToMany(Atividade::class, 'atividade_projeto');
    }

    public function tarefas(): BelongsToMany
    {
        return $this->belongsToMany(Tarefa::class, 'tarefa_projeto');
    }

    public function metodoFerramentas(): BelongsToMany
    {
        return $this->belongsToMany(MetodoFerramenta::class, 'metodo_ferramenta_projeto');
    }
}
