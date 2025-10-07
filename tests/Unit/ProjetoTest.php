<?php

namespace Tests\Unit;

use App\Models\Atividade;
use App\Models\Fase;
use App\Models\MetodoFerramenta;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\TestCase;

class ProjetoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo possui as traits necessárias.
     */
    public function test_projeto_model_has_required_traits(): void
    {
        $projeto = new Projeto();
        $this->assertContains(HasFactory::class, class_uses($projeto));
    }

    /**
     * Testa se os atributos fillable estão configurados corretamente.
     */
    public function test_projeto_model_has_correct_fillable_attributes(): void
    {
        $projeto = new Projeto();
        $expectedFillable = ['nome', 'descricao', 'user_id'];
        
        $this->assertEquals($expectedFillable, $projeto->getFillable());
    }

    /**
     * Testa se o modelo usa a tabela correta.
     */
    public function test_projeto_model_uses_correct_table(): void
    {
        $projeto = new Projeto();
        $this->assertEquals('projetos', $projeto->getTable());
    }

    /**
     * Testa se a chave primária está configurada corretamente.
     */
    public function test_projeto_model_has_correct_primary_key(): void
    {
        $projeto = new Projeto();
        $this->assertEquals('id', $projeto->getKeyName());
    }

    /**
     * Testa se é possível criar um projeto com dados válidos.
     */
    public function test_can_create_projeto_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $projetoData = [
            'nome' => 'Projeto Teste',
            'descricao' => 'Descrição do projeto teste',
            'user_id' => $user->id,
        ];

        $projeto = Projeto::create($projetoData);

        $this->assertInstanceOf(Projeto::class, $projeto);
        $this->assertEquals('Projeto Teste', $projeto->nome);
        $this->assertEquals('Descrição do projeto teste', $projeto->descricao);
        $this->assertEquals($user->id, $projeto->user_id);
        $this->assertDatabaseHas('projetos', $projetoData);
    }

    /**
     * Testa se é possível usar a factory do projeto.
     */
    public function test_can_use_projeto_factory(): void
    {
        $projeto = Projeto::factory()->create();

        $this->assertInstanceOf(Projeto::class, $projeto);
        $this->assertNotEmpty($projeto->nome);
        $this->assertNotEmpty($projeto->descricao);
        $this->assertNotNull($projeto->user_id);
        $this->assertDatabaseHas('projetos', ['id' => $projeto->id]);
    }

    /**
     * Testa se é possível criar múltiplos projetos com factory.
     */
    public function test_can_create_multiple_projetos_with_factory(): void
    {
        $projetos = Projeto::factory()->count(3)->create();

        $this->assertCount(3, $projetos);
        $this->assertEquals(3, Projeto::count());
        
        foreach ($projetos as $projeto) {
            $this->assertInstanceOf(Projeto::class, $projeto);
        }
    }

    /**
     * Testa se campos obrigatórios não podem ser nulos.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Projeto::create([
            'nome' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar um projeto.
     */
    public function test_can_update_projeto(): void
    {
        $projeto = Projeto::factory()->create();
        $novoNome = 'Projeto Atualizado';
        
        $projeto->update(['nome' => $novoNome]);
        
        $this->assertEquals($novoNome, $projeto->fresh()->nome);
        $this->assertDatabaseHas('projetos', [
            'id' => $projeto->id,
            'nome' => $novoNome,
        ]);
    }

    /**
     * Testa se é possível deletar um projeto.
     */
    public function test_can_delete_projeto(): void
    {
        $projeto = Projeto::factory()->create();
        $projetoId = $projeto->id;
        
        $projeto->delete();
        
        $this->assertDatabaseMissing('projetos', ['id' => $projetoId]);
        $this->assertEquals(0, Projeto::count());
    }

    /**
     * Testa o relacionamento com User.
     */
    public function test_projeto_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $projeto = Projeto::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $projeto->user);
        $this->assertEquals($user->id, $projeto->user->id);
        $this->assertEquals($user->name, $projeto->user->name);
    }

    /**
     * Testa o relacionamento many-to-many com Fases.
     */
    public function test_projeto_has_many_to_many_relationship_with_fases(): void
    {
        $projeto = Projeto::factory()->create();
        $fase = Fase::factory()->create(['user_id' => $projeto->user_id]);

        $projeto->fases()->attach($fase);

        $this->assertTrue($projeto->fases->contains($fase));
        $this->assertInstanceOf(Fase::class, $projeto->fases->first());
        $this->assertEquals(1, $projeto->fases->count());
    }

    /**
     * Testa o relacionamento many-to-many com Atividades.
     */
    public function test_projeto_has_many_to_many_relationship_with_atividades(): void
    {
        $projeto = Projeto::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $projeto->user_id]);

        $projeto->atividades()->attach($atividade);

        $this->assertTrue($projeto->atividades->contains($atividade));
        $this->assertInstanceOf(Atividade::class, $projeto->atividades->first());
        $this->assertEquals(1, $projeto->atividades->count());
    }

    /**
     * Testa o relacionamento many-to-many com Tarefas.
     */
    public function test_projeto_has_many_to_many_relationship_with_tarefas(): void
    {
        $projeto = Projeto::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $projeto->user_id]);

        $projeto->tarefas()->attach($tarefa);

        $this->assertTrue($projeto->tarefas->contains($tarefa));
        $this->assertInstanceOf(Tarefa::class, $projeto->tarefas->first());
        $this->assertEquals(1, $projeto->tarefas->count());
    }

    /**
     * Testa o relacionamento many-to-many com MetodoFerramentas.
     */
    public function test_projeto_has_many_to_many_relationship_with_metodo_ferramentas(): void
    {
        $projeto = Projeto::factory()->create();
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $projeto->user_id]);

        $projeto->metodoFerramentas()->attach($metodo);

        $this->assertTrue($projeto->metodoFerramentas->contains($metodo));
        $this->assertInstanceOf(MetodoFerramenta::class, $projeto->metodoFerramentas->first());
        $this->assertEquals(1, $projeto->metodoFerramentas->count());
    }

    /**
     * Testa todos os relacionamentos many-to-many em conjunto.
     */
    public function test_projeto_has_all_many_to_many_relationships(): void
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
        
        $this->assertEquals(1, $projeto->fases->count());
        $this->assertEquals(1, $projeto->atividades->count());
        $this->assertEquals(1, $projeto->tarefas->count());
        $this->assertEquals(1, $projeto->metodoFerramentas->count());
    }
}
