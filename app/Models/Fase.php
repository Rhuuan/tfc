<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'data',
    ];

    // Relação muitos para muitos com Atividade
    public function atividades()
    {
        return $this->belongsToMany(Atividade::class);
    }
}
