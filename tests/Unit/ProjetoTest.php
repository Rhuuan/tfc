<?php

namespace Tests\Unit;

use App\Models\Atividade;
use App\Models\Fase;
use App\Models\MetodoFerramenta;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cria_projeto_com_user_id()
    {
        $user = User::factory()->create();
        $projeto = Projeto::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('projetos', [
            'id' => $projeto->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function relacionamento_user()
    {
        $user = User::factory()->create();
        $projeto = Projeto::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $projeto->user);
        $this->assertEquals($user->id, $projeto->user->id);
    }

    /** @test */
    public function relacionamentos_muitos_para_muitos()
    {
        $projeto = Projeto::factory()->create();

        $fase = Fase::factory()->create(['user_id' => $projeto->user_id]);
        $atividade = Atividade::factory()->create(['user_id' => $projeto->user_id]);
        $tarefa = Tarefa::factory()->create(['user_id' => $projeto->user_id]);
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $projeto->user_id]);

        $projeto->fases()->attach($fase);
        $projeto->atividades()->attach($atividade);
        $projeto->tarefas()->attach($tarefa);
        $projeto->metodoFerramentas()->attach($metodo);

        $this->assertTrue($projeto->fases->contains($fase));
        $this->assertTrue($projeto->atividades->contains($atividade));
        $this->assertTrue($projeto->tarefas->contains($tarefa));
        $this->assertTrue($projeto->metodoFerramentas->contains($metodo));
    }

    /** @test */
    public function campos_obrigatorios()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Projeto::create([]);
    }
}
