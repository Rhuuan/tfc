<?php

namespace Database\Factories;

use App\Models\Projeto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetoFactory extends Factory
{
    protected $model = Projeto::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(),
            'user_id' => User::factory(), // garante que sempre há um usuário válido
        ];
    }
}
