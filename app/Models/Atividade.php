<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'tarefa_id',
        'fase_id',
        'user_id', // 🔹 adicionado
    ];

    public function tarefa()
    {
        return $this->belongsTo(Tarefa::class);
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
