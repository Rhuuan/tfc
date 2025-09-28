<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AtividadeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cria_atividade_com_user_id()
    {
        $atividade = Atividade::factory()->create();
        $this->assertNotNull($atividade->user_id);
        $this->assertNotNull($atividade->tarefa_id);
        $this->assertNotNull($atividade->fase_id);
    }

    /** @test */
    public function relacionamento_user()
    {
        $atividade = Atividade::factory()->create();
        $this->assertInstanceOf(User::class, $atividade->user);
    }

    /** @test */
    public function campos_obrigatorios()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Atividade::create([]); // deve falhar pois campos obrigatórios não são preenchidos
    }
}
