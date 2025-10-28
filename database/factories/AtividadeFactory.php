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
            'fase_id' => Fase::factory(),     // cria uma fase automaticamente
            'user_id' => User::factory(),     // cria um usuário automaticamente
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Atividade $atividade): void {
            if ($atividade->tarefas()->exists()) {
                return;
            }

            $atividade->tarefas()->attach(
                Tarefa::factory()->create(['user_id' => $atividade->user_id])->id
            );
        });
    }
}
