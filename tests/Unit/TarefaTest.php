<?php

namespace Tests\Unit;

use App\Models\Tarefa;
use App\Models\MetodoFerramenta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function tarefa_pode_ser_associada_a_metodos_ferramentas()
    {
        // Criar usuário via factory
        $user = User::factory()->create();

        // Criar uma tarefa associada a esse usuário
        $tarefa = Tarefa::create([
            'nome' => 'Tarefa de Teste',
            'descricao' => 'Descrição da tarefa de teste',
            'user_id' => $user->id,
        ]);

        // Criar um método/ferramenta também vinculado ao usuário
        $metodo = MetodoFerramenta::create([
            'nome' => 'Ferramenta X',
            'tipo' => 'tecnica',
            'descricao' => 'Descrição da ferramenta',
            'user_id' => $user->id,
        ]);

        // Associar os dois
        $tarefa->metodosFerramentas()->attach($metodo->id);

        // Recarregar a relação
        $tarefa->load('metodosFerramentas');

        // Garantir que a tarefa tem o método associado
        $this->assertTrue($tarefa->metodosFerramentas->contains($metodo));
    }
}
