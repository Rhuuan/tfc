<?php

namespace Database\Factories;

use App\Models\MetodoFerramenta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetodoFerramentaFactory extends Factory
{
    protected $model = MetodoFerramenta::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->word(),
            'tipo' => $this->faker->randomElement(['tecnica', 'ferramenta']),
            'descricao' => $this->faker->sentence(),
            'user_id' => User::factory(), // Garante que sempre haverá um user_id válido
        ];
    }
}
