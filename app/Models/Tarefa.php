<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tarefa extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'user_id', // adiciona user_id
    ];

    /**
     * Relacionamento com o usuário dono da tarefa
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com métodos e ferramentas
     */
    public function metodosFerramentas(): BelongsToMany
    {
        return $this->belongsToMany(MetodoFerramenta::class, 'metodo_ferramenta_tarefa');
    }
}
