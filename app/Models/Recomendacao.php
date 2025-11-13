<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recomendacao extends Model
{
    use HasFactory;

    protected $table = 'recomendacoes';

    protected $fillable = [
        'projeto_id',
        'user_id',
        'codigo_mermaid',
        'justificacao',
        'resposta_bruta',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
