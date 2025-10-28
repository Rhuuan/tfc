<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Atividade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'fase_id',
        'user_id', // 🔹 adicionado
    ];

    public function tarefas(): BelongsToMany
    {
        return $this->belongsToMany(Tarefa::class, 'atividade_tarefa')->withTimestamps();
    }

    public function fase()
    {
        return $this->belongsTo(Fase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // 🔹 vínculo com o usuário
    }
}
