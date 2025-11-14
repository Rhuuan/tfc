<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecomendacaoFeedback extends Model
{
    use HasFactory;

    protected $table = 'recomendacao_feedbacks';

    protected $fillable = [
        'recomendacao_id',
        'user_id',
        'value', // 'like' | 'dislike'
    ];

    public function recomendacao(): BelongsTo
    {
        return $this->belongsTo(Recomendacao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
