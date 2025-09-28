<?php

namespace Database\Factories;

use App\Models\Atividade;
use App\Models\Tarefa;
use App\Models\Fase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtividadeFactory extends Factory
{
    protected $model = Atividade::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'descricao' => $this->faker->sentence,
            'tarefa_id' => Tarefa::factory(), // cria uma tarefa automaticamente
            'fase_id' => Fase::factory(),     // cria uma fase automaticamente
            'user_id' => User::factory(),     // cria um usuário automaticamente
        ];
    }
}
