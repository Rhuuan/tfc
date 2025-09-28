<?php

namespace Database\Factories;

use App\Models\Fase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaseFactory extends Factory
{
    protected $model = Fase::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->word,
            'data' => $this->faker->date(),
            'user_id' => User::factory(), // vincula usuário automaticamente
        ];
    }
}
