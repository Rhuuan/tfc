<?php

namespace Tests\Unit;

use App\Models\Tarefa;
use App\Models\MetodoFerramenta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\TestCase;

class TarefaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo possui as traits necessárias.
     */
    public function test_tarefa_model_has_required_traits(): void
    {
        $tarefa = new Tarefa();
        $this->assertContains(HasFactory::class, class_uses($tarefa));
    }

    /**
     * Testa se os atributos fillable estão configurados corretamente.
     */
    public function test_tarefa_model_has_correct_fillable_attributes(): void
    {
        $tarefa = new Tarefa();
        $expectedFillable = ['nome', 'descricao', 'user_id'];
        
        $this->assertEquals($expectedFillable, $tarefa->getFillable());
    }

    /**
     * Testa se o modelo usa a tabela correta.
     */
    public function test_tarefa_model_uses_correct_table(): void
    {
        $tarefa = new Tarefa();
        $this->assertEquals('tarefas', $tarefa->getTable());
    }

    /**
     * Testa se a chave primária está configurada corretamente.
     */
    public function test_tarefa_model_has_correct_primary_key(): void
    {
        $tarefa = new Tarefa();
        $this->assertEquals('id', $tarefa->getKeyName());
    }

    /**
     * Testa se é possível criar uma tarefa com dados válidos.
     */
    public function test_can_create_tarefa_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $tarefaData = [
            'nome' => 'Implementar Sistema de Login',
            'descricao' => 'Desenvolver autenticação de usuários com Laravel',
            'user_id' => $user->id,
        ];

        $tarefa = Tarefa::create($tarefaData);

        $this->assertInstanceOf(Tarefa::class, $tarefa);
        $this->assertEquals('Implementar Sistema de Login', $tarefa->nome);
        $this->assertEquals('Desenvolver autenticação de usuários com Laravel', $tarefa->descricao);
        $this->assertEquals($user->id, $tarefa->user_id);
        $this->assertDatabaseHas('tarefas', $tarefaData);
    }

    /**
     * Testa se é possível usar a factory da tarefa.
     */
    public function test_can_use_tarefa_factory(): void
    {
        $tarefa = Tarefa::factory()->create();

        $this->assertInstanceOf(Tarefa::class, $tarefa);
        $this->assertNotEmpty($tarefa->nome);
        $this->assertNotEmpty($tarefa->descricao);
        $this->assertNotNull($tarefa->user_id);
        $this->assertDatabaseHas('tarefas', ['id' => $tarefa->id]);
    }

    /**
     * Testa se é possível criar múltiplas tarefas com factory.
     */
    public function test_can_create_multiple_tarefas_with_factory(): void
    {
        $tarefas = Tarefa::factory()->count(3)->create();

        $this->assertCount(3, $tarefas);
        $this->assertEquals(3, Tarefa::count());
        
        foreach ($tarefas as $tarefa) {
            $this->assertInstanceOf(Tarefa::class, $tarefa);
        }
    }

    /**
     * Testa se campos obrigatórios não podem ser nulos.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Tarefa::create([
            'nome' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar uma tarefa.
     */
    public function test_can_update_tarefa(): void
    {
        $tarefa = Tarefa::factory()->create();
        $novoNome = 'Tarefa Atualizada';
        $novaDescricao = 'Descrição atualizada da tarefa';
        
        $tarefa->update([
            'nome' => $novoNome,
            'descricao' => $novaDescricao,
        ]);
        
        $this->assertEquals($novoNome, $tarefa->fresh()->nome);
        $this->assertEquals($novaDescricao, $tarefa->fresh()->descricao);
        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefa->id,
            'nome' => $novoNome,
            'descricao' => $novaDescricao,
        ]);
    }

    /**
     * Testa se é possível deletar uma tarefa.
     */
    public function test_can_delete_tarefa(): void
    {
        $tarefa = Tarefa::factory()->create();
        $tarefaId = $tarefa->id;
        
        $tarefa->delete();
        
        $this->assertDatabaseMissing('tarefas', ['id' => $tarefaId]);
        $this->assertEquals(0, Tarefa::count());
    }

    /**
     * Testa o relacionamento com User.
     */
    public function test_tarefa_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $tarefa->user);
        $this->assertEquals($user->id, $tarefa->user->id);
        $this->assertEquals($user->name, $tarefa->user->name);
    }

    /**
     * Testa o relacionamento many-to-many com MetodoFerramentas.
     */
    public function test_tarefa_has_many_to_many_relationship_with_metodo_ferramentas(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $user->id]);

        $tarefa->metodosFerramentas()->attach($metodo);

        $this->assertTrue($tarefa->metodosFerramentas->contains($metodo));
        $this->assertInstanceOf(MetodoFerramenta::class, $tarefa->metodosFerramentas->first());
        $this->assertEquals(1, $tarefa->metodosFerramentas->count());
    }

    /**
     * Testa se uma tarefa pode ter múltiplos métodos/ferramentas.
     */
    public function test_tarefa_can_have_multiple_metodo_ferramentas(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);
        $metodos = MetodoFerramenta::factory()->count(3)->create(['user_id' => $user->id]);

        foreach ($metodos as $metodo) {
            $tarefa->metodosFerramentas()->attach($metodo);
        }

        $this->assertEquals(3, $tarefa->metodosFerramentas->count());
        
        foreach ($metodos as $metodo) {
            $this->assertTrue($tarefa->metodosFerramentas->contains($metodo));
        }
    }

    /**
     * Testa se é possível desassociar métodos/ferramentas de uma tarefa.
     */
    public function test_can_detach_metodo_ferramentas_from_tarefa(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $user->id]);

        // Associar primeiro
        $tarefa->metodosFerramentas()->attach($metodo);
        $this->assertEquals(1, $tarefa->metodosFerramentas->count());

        // Desassociar depois
        $tarefa->metodosFerramentas()->detach($metodo);
        $this->assertEquals(0, $tarefa->fresh()->metodosFerramentas->count());
    }

    /**
     * Testa associação usando o método original preservado.
     */
    public function test_tarefa_pode_ser_associada_a_metodos_ferramentas(): void
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

    /**
     * Testa se user_id é obrigatório ao criar uma tarefa.
     */
    public function test_user_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Tarefa::create([
            'nome' => 'Tarefa sem usuário',
            'descricao' => 'Descrição da tarefa',
            // user_id está faltando
        ]);
    }

    /**
     * Testa se nome é obrigatório ao criar uma tarefa.
     */
    public function test_nome_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $user = User::factory()->create();
        
        Tarefa::create([
            'descricao' => 'Descrição da tarefa',
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
        
        $tarefa = Tarefa::create([
            'nome' => 'Tarefa sem descrição',
            'user_id' => $user->id,
            // descrição não fornecida
        ]);

        $this->assertInstanceOf(Tarefa::class, $tarefa);
        $this->assertEquals('Tarefa sem descrição', $tarefa->nome);
        $this->assertNull($tarefa->descricao);
    }

    /**
     * Testa sincronização de métodos/ferramentas.
     */
    public function test_can_sync_metodo_ferramentas(): void
    {
        $user = User::factory()->create();
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);
        $metodos = MetodoFerramenta::factory()->count(3)->create(['user_id' => $user->id]);

        // Sincronizar todos
        $tarefa->metodosFerramentas()->sync($metodos->pluck('id'));
        $this->assertEquals(3, $tarefa->metodosFerramentas->count());

        // Sincronizar apenas alguns
        $tarefa->metodosFerramentas()->sync([$metodos->first()->id]);
        $this->assertEquals(1, $tarefa->fresh()->metodosFerramentas->count());
    }
}
