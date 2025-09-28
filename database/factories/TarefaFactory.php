<?php

namespace Database\Factories;

use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarefaFactory extends Factory
{
    protected $model = Tarefa::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'descricao' => $this->faker->sentence,
            'user_id' => User::factory(), // garante um usuário válido
        ];
    }
}
