<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fase extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'user_id',
    ];

    // Relação muitos para muitos com Atividade
    public function atividades()
    {
        return $this->belongsToMany(Atividade::class);
    }

    /**
     * Relacionamento com o usuário dono da fase
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
