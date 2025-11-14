<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Feedbacks dados pelos usuários para esta recomendação
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(RecomendacaoFeedback::class, 'recomendacao_id');
    }
}
