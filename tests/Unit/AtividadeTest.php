<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Atividade;
use App\Models\Fase;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtividadeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo possui as traits necessárias.
     */
    public function test_atividade_model_has_required_traits(): void
    {
        $atividade = new Atividade();
        $this->assertContains(HasFactory::class, class_uses($atividade));
    }

    /**
     * Testa se os atributos fillable estão configurados corretamente.
     */
    public function test_atividade_model_has_correct_fillable_attributes(): void
    {
        $atividade = new Atividade();
        $expectedFillable = ['nome', 'descricao', 'fase_id', 'user_id'];
        
        $this->assertEquals($expectedFillable, $atividade->getFillable());
    }

    /**
     * Testa se o modelo usa a tabela correta.
     */
    public function test_atividade_model_uses_correct_table(): void
    {
        $atividade = new Atividade();
        $this->assertEquals('atividades', $atividade->getTable());
    }

    /**
     * Testa se a chave primária está configurada corretamente.
     */
    public function test_atividade_model_has_correct_primary_key(): void
    {
        $atividade = new Atividade();
        $this->assertEquals('id', $atividade->getKeyName());
    }

    /**
     * Testa se é possível criar uma atividade com dados válidos.
     */
    public function test_can_create_atividade_with_valid_data(): void
    {
        $user = User::factory()->create();
        $tarefas = Tarefa::factory()->count(2)->create(['user_id' => $user->id]);
        $fase = Fase::factory()->create(['user_id' => $user->id]);
        
        $atividadeData = [
            'nome' => 'Análise de Requisitos',
            'descricao' => 'Levantar e documentar os requisitos do sistema',
            'fase_id' => $fase->id,
            'user_id' => $user->id,
        ];

        $atividade = Atividade::create($atividadeData);

    $atividade->tarefas()->attach($tarefas->pluck('id')->all());

        $this->assertInstanceOf(Atividade::class, $atividade);
        $this->assertEquals('Análise de Requisitos', $atividade->nome);
        $this->assertEquals('Levantar e documentar os requisitos do sistema', $atividade->descricao);
        $this->assertEquals($fase->id, $atividade->fase_id);
        $this->assertEquals($user->id, $atividade->user_id);
        $this->assertEquals(2, $atividade->tarefas()->count());

        foreach ($tarefas as $tarefa) {
            $this->assertDatabaseHas('atividade_tarefa', [
                'atividade_id' => $atividade->id,
                'tarefa_id' => $tarefa->id,
            ]);
        }

        $this->assertDatabaseHas('atividades', [
            'id' => $atividade->id,
            'nome' => 'Análise de Requisitos',
            'descricao' => 'Levantar e documentar os requisitos do sistema',
            'fase_id' => $fase->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Testa se é possível usar a factory da atividade.
     */
    public function test_can_use_atividade_factory(): void
    {
        $atividade = Atividade::factory()->create();

        $this->assertInstanceOf(Atividade::class, $atividade);
        $this->assertNotEmpty($atividade->nome);
        $this->assertNotEmpty($atividade->descricao);
        $this->assertNotNull($atividade->fase_id);
        $this->assertNotNull($atividade->user_id);
        $this->assertTrue($atividade->tarefas()->exists());
        $this->assertDatabaseHas('atividades', ['id' => $atividade->id]);
    }

    /**
     * Testa se é possível criar múltiplas atividades com factory.
     */
    public function test_can_create_multiple_atividades_with_factory(): void
    {
        $atividades = Atividade::factory()->count(3)->create();

        $this->assertCount(3, $atividades);
        $this->assertEquals(3, Atividade::count());
        
        foreach ($atividades as $atividade) {
            $this->assertInstanceOf(Atividade::class, $atividade);
        }
    }

    /**
     * Testa se campos obrigatórios não podem ser nulos.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Atividade::create([
            'nome' => null,
            'fase_id' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar uma atividade.
     */
    public function test_can_update_atividade(): void
    {
        $atividade = Atividade::factory()->create();
        $novoNome = 'Atividade Atualizada';
        $novaDescricao = 'Descrição atualizada da atividade';
        
        $atividade->update([
            'nome' => $novoNome,
            'descricao' => $novaDescricao,
        ]);
        
        $this->assertEquals($novoNome, $atividade->fresh()->nome);
        $this->assertEquals($novaDescricao, $atividade->fresh()->descricao);
        $this->assertDatabaseHas('atividades', [
            'id' => $atividade->id,
            'nome' => $novoNome,
            'descricao' => $novaDescricao,
        ]);
    }

    /**
     * Testa se é possível deletar uma atividade.
     */
    public function test_can_delete_atividade(): void
    {
        $atividade = Atividade::factory()->create();
        $atividadeId = $atividade->id;
        
        $atividade->delete();
        
        $this->assertDatabaseMissing('atividades', ['id' => $atividadeId]);
        $this->assertEquals(0, Atividade::count());
    }

    /**
     * Testa o relacionamento com User.
     */
    public function test_atividade_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $atividade->user);
        $this->assertEquals($user->id, $atividade->user->id);
        $this->assertEquals($user->name, $atividade->user->name);
    }

    /**
     * Testa o relacionamento com Tarefa.
     */
    public function test_atividade_belongs_to_many_tarefas(): void
    {
        $user = User::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $user->id]);
        $tarefas = Tarefa::factory()->count(2)->create(['user_id' => $user->id]);

        $atividade->tarefas()->sync($tarefas->pluck('id'));
        $atividade->load('tarefas');

        $this->assertEquals(2, $atividade->tarefas->count());

        foreach ($tarefas as $tarefa) {
            $this->assertTrue($atividade->tarefas->contains($tarefa));
        }
    }

    /**
     * Testa o relacionamento com Fase.
     */
    public function test_atividade_belongs_to_fase(): void
    {
        $user = User::factory()->create();
        $fase = Fase::factory()->create(['user_id' => $user->id]);
        $atividade = Atividade::factory()->create([
            'fase_id' => $fase->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Fase::class, $atividade->fase);
        $this->assertEquals($fase->id, $atividade->fase->id);
        $this->assertEquals($fase->nome, $atividade->fase->nome);
    }

    /**
     * Testa todos os relacionamentos em conjunto.
     */
    public function test_atividade_has_all_relationships(): void
    {
        $user = User::factory()->create();
        $tarefas = Tarefa::factory()->count(2)->create(['user_id' => $user->id]);
        $fase = Fase::factory()->create(['user_id' => $user->id]);
        
        $atividade = Atividade::factory()->create([
            'user_id' => $user->id,
            'fase_id' => $fase->id,
        ]);

        $atividade->tarefas()->sync($tarefas->pluck('id'));
        $atividade->load('tarefas');

        // Testa todos os relacionamentos
        $this->assertInstanceOf(User::class, $atividade->user);
        $this->assertInstanceOf(Fase::class, $atividade->fase);
        $this->assertEquals($tarefas->count(), $atividade->tarefas->count());
        
        // Testa integridade dos dados
        $this->assertEquals($user->id, $atividade->user->id);
        $this->assertEquals($fase->id, $atividade->fase->id);
        foreach ($tarefas as $tarefa) {
            $this->assertTrue($atividade->tarefas->contains($tarefa));
        }
    }

    /**
     * Testa se user_id é obrigatório ao criar uma atividade.
     */
    public function test_user_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $fase = Fase::factory()->create();
        
        Atividade::create([
            'nome' => 'Atividade sem usuário',
            'descricao' => 'Descrição da atividade',
            'fase_id' => $fase->id,
            // user_id está faltando
        ]);
    }

    /**
     * Testa se uma atividade pode ter múltiplas tarefas associadas.
     */
    public function test_atividade_can_have_multiple_tarefas(): void
    {
        $user = User::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $user->id]);
        $tarefas = Tarefa::factory()->count(3)->create(['user_id' => $user->id]);

        $atividade->tarefas()->sync($tarefas->pluck('id'));
        $atividade->load('tarefas');

        $this->assertEquals(3, $atividade->tarefas->count());

        foreach ($tarefas as $tarefa) {
            $this->assertTrue($atividade->tarefas->contains($tarefa));
        }
    }

    /**
     * Testa se fase_id pode ser opcional ao criar uma atividade.
     */
    public function test_fase_id_can_be_optional(): void
    {
        $user = User::factory()->create();

        $atividade = Atividade::create([
            'nome' => 'Atividade sem fase',
            'descricao' => 'Descrição da atividade',
            'user_id' => $user->id,
        ]);

        $this->assertNull($atividade->fase_id);
        $this->assertDatabaseHas('atividades', [
            'id' => $atividade->id,
            'fase_id' => null,
        ]);
    }

    /**
     * Testa se nome é obrigatório ao criar uma atividade.
     */
    public function test_nome_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $user = User::factory()->create();
        $fase = Fase::factory()->create(['user_id' => $user->id]);
        
        Atividade::create([
            'descricao' => 'Descrição da atividade',
            'fase_id' => $fase->id,
            'user_id' => $user->id,
            // nome está faltando
        ]);
    }

    /**
     * Testa se descrição pode ser opcional.
     */
    public function test_descricao_can_be_optional(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);
        $fase = Fase::factory()->create(['user_id' => $user->id]);
        
        $atividade = Atividade::create([
            'nome' => 'Atividade sem descrição',
            'fase_id' => $fase->id,
            'user_id' => $user->id,
            // descrição não fornecida
        ]);

        $atividade->tarefas()->attach($tarefa->id);

        $this->assertInstanceOf(Atividade::class, $atividade);
        $this->assertEquals('Atividade sem descrição', $atividade->nome);
        $this->assertNull($atividade->descricao);
    }
}
