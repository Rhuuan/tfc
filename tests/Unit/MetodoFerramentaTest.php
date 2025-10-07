<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\MetodoFerramenta;
use App\Models\Tarefa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\TestCase;

class MetodoFerramentaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo possui as traits necessárias.
     */
    public function test_metodo_ferramenta_model_has_required_traits(): void
    {
        $metodo = new MetodoFerramenta();
        $this->assertContains(HasFactory::class, class_uses($metodo));
    }

    /**
     * Testa se os atributos fillable estão configurados corretamente.
     */
    public function test_metodo_ferramenta_model_has_correct_fillable_attributes(): void
    {
        $metodo = new MetodoFerramenta();
        $expectedFillable = ['nome', 'tipo', 'descricao', 'user_id'];
        
        $this->assertEquals($expectedFillable, $metodo->getFillable());
    }

    /**
     * Testa se o modelo usa a tabela correta.
     */
    public function test_metodo_ferramenta_model_uses_correct_table(): void
    {
        $metodo = new MetodoFerramenta();
        $this->assertEquals('metodo_ferramentas', $metodo->getTable());
    }

    /**
     * Testa se a chave primária está configurada corretamente.
     */
    public function test_metodo_ferramenta_model_has_correct_primary_key(): void
    {
        $metodo = new MetodoFerramenta();
        $this->assertEquals('id', $metodo->getKeyName());
    }

    /**
     * Testa se é possível criar um método/ferramenta com dados válidos.
     */
    public function test_can_create_metodo_ferramenta_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $metodoData = [
            'nome' => 'Scrum',
            'tipo' => 'tecnica',
            'descricao' => 'Metodologia ágil para desenvolvimento de software',
            'user_id' => $user->id,
        ];

        $metodo = MetodoFerramenta::create($metodoData);

        $this->assertInstanceOf(MetodoFerramenta::class, $metodo);
        $this->assertEquals('Scrum', $metodo->nome);
        $this->assertEquals('tecnica', $metodo->tipo);
        $this->assertEquals('Metodologia ágil para desenvolvimento de software', $metodo->descricao);
        $this->assertEquals($user->id, $metodo->user_id);
        $this->assertDatabaseHas('metodo_ferramentas', $metodoData);
    }

    /**
     * Testa se é possível usar a factory do método/ferramenta.
     */
    public function test_can_use_metodo_ferramenta_factory(): void
    {
        $metodo = MetodoFerramenta::factory()->create();

        $this->assertInstanceOf(MetodoFerramenta::class, $metodo);
        $this->assertNotEmpty($metodo->nome);
        $this->assertContains($metodo->tipo, ['tecnica', 'ferramenta']);
        $this->assertNotEmpty($metodo->descricao);
        $this->assertNotNull($metodo->user_id);
        $this->assertDatabaseHas('metodo_ferramentas', ['id' => $metodo->id]);
    }

    /**
     * Testa se é possível criar múltiplos métodos/ferramentas com factory.
     */
    public function test_can_create_multiple_metodo_ferramentas_with_factory(): void
    {
        $metodos = MetodoFerramenta::factory()->count(3)->create();

        $this->assertCount(3, $metodos);
        $this->assertEquals(3, MetodoFerramenta::count());
        
        foreach ($metodos as $metodo) {
            $this->assertInstanceOf(MetodoFerramenta::class, $metodo);
        }
    }

    /**
     * Testa se campos obrigatórios não podem ser nulos.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        MetodoFerramenta::create([
            'nome' => null,
            'tipo' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar um método/ferramenta.
     */
    public function test_can_update_metodo_ferramenta(): void
    {
        $metodo = MetodoFerramenta::factory()->create();
        $novoNome = 'Kanban';
        $novoTipo = 'ferramenta';
        $novaDescricao = 'Sistema de gestão visual de tarefas';
        
        $metodo->update([
            'nome' => $novoNome,
            'tipo' => $novoTipo,
            'descricao' => $novaDescricao,
        ]);
        
        $this->assertEquals($novoNome, $metodo->fresh()->nome);
        $this->assertEquals($novoTipo, $metodo->fresh()->tipo);
        $this->assertEquals($novaDescricao, $metodo->fresh()->descricao);
        $this->assertDatabaseHas('metodo_ferramentas', [
            'id' => $metodo->id,
            'nome' => $novoNome,
            'tipo' => $novoTipo,
            'descricao' => $novaDescricao,
        ]);
    }

    /**
     * Testa se é possível deletar um método/ferramenta.
     */
    public function test_can_delete_metodo_ferramenta(): void
    {
        $metodo = MetodoFerramenta::factory()->create();
        $metodoId = $metodo->id;
        
        $metodo->delete();
        
        $this->assertDatabaseMissing('metodo_ferramentas', ['id' => $metodoId]);
        $this->assertEquals(0, MetodoFerramenta::count());
    }

    /**
     * Testa o relacionamento com User.
     */
    public function test_metodo_ferramenta_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $metodo->user);
        $this->assertEquals($user->id, $metodo->user->id);
        $this->assertEquals($user->name, $metodo->user->name);
    }

    /**
     * Testa se user_id é obrigatório ao criar um método/ferramenta.
     */
    public function test_user_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        MetodoFerramenta::create([
            'nome' => 'Método sem usuário',
            'tipo' => 'tecnica',
            'descricao' => 'Descrição do método',
            // user_id está faltando
        ]);
    }

    /**
     * Testa se nome é obrigatório ao criar um método/ferramenta.
     */
    public function test_nome_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $user = User::factory()->create();
        
        MetodoFerramenta::create([
            'tipo' => 'tecnica',
            'descricao' => 'Descrição do método',
            'user_id' => $user->id,
            // nome está faltando
        ]);
    }

    /**
     * Testa se tipo é obrigatório ao criar um método/ferramenta.
     */
    public function test_tipo_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $user = User::factory()->create();
        
        MetodoFerramenta::create([
            'nome' => 'Método sem tipo',
            'descricao' => 'Descrição do método',
            'user_id' => $user->id,
            // tipo está faltando
        ]);
    }

    /**
     * Testa se descrição pode ser opcional.
     */
    public function test_descricao_can_be_optional(): void
    {
        $user = User::factory()->create();
        
        $metodo = MetodoFerramenta::create([
            'nome' => 'Método sem descrição',
            'tipo' => 'ferramenta',
            'user_id' => $user->id,
            // descrição não fornecida
        ]);

        $this->assertInstanceOf(MetodoFerramenta::class, $metodo);
        $this->assertEquals('Método sem descrição', $metodo->nome);
        $this->assertEquals('ferramenta', $metodo->tipo);
        $this->assertNull($metodo->descricao);
    }

    /**
     * Testa se tipos válidos são aceitos.
     */
    public function test_accepts_valid_tipos(): void
    {
        $user = User::factory()->create();
        
        // Teste tipo 'tecnica'
        $tecnica = MetodoFerramenta::create([
            'nome' => 'Scrum',
            'tipo' => 'tecnica',
            'descricao' => 'Metodologia ágil',
            'user_id' => $user->id,
        ]);
        
        // Teste tipo 'ferramenta'
        $ferramenta = MetodoFerramenta::create([
            'nome' => 'Jira',
            'tipo' => 'ferramenta',
            'descricao' => 'Software de gestão',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('tecnica', $tecnica->tipo);
        $this->assertEquals('ferramenta', $ferramenta->tipo);
    }

    /**
     * Testa se factory gera tipos válidos.
     */
    public function test_factory_generates_valid_tipos(): void
    {
        $metodos = MetodoFerramenta::factory()->count(10)->create();
        
        foreach ($metodos as $metodo) {
            $this->assertContains($metodo->tipo, ['tecnica', 'ferramenta']);
        }
    }

    /**
     * Testa relacionamento many-to-many com Tarefas (se existir).
     */
    public function test_metodo_ferramenta_can_be_associated_with_tarefas(): void
    {
        $user = User::factory()->create();
        $metodo = MetodoFerramenta::factory()->create(['user_id' => $user->id]);
        $tarefa = Tarefa::factory()->create(['user_id' => $user->id]);

        // Assumindo que existe relacionamento many-to-many
        $tarefa->metodosFerramentas()->attach($metodo);

        // Verificar se o método foi associado à tarefa
        $this->assertTrue($tarefa->metodosFerramentas->contains($metodo));
    }

    /**
     * Teste original preservado.
     */
    public function test_cria_metodo_ferramenta_com_user_id(): void
    {
        // Cria um usuário
        $user = User::factory()->create();

        // Cria o MetodoFerramenta associado ao usuário
        $metodo = MetodoFerramenta::factory()->for($user)->create();

        $this->assertDatabaseHas('metodo_ferramentas', [
            'id' => $metodo->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Teste original preservado.
     */
    public function test_relacionamento_user(): void
    {
        $user = User::factory()->create();
        $metodo = MetodoFerramenta::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $metodo->user);
        $this->assertEquals($user->id, $metodo->user->id);
    }

    /**
     * Teste original preservado.
     */
    public function test_campos_obrigatorios(): void
    {
        $user = User::factory()->create();

        $metodo = MetodoFerramenta::factory()->make([
            'user_id' => $user->id,
        ]);

        $this->assertNotEmpty($metodo->nome);
        $this->assertNotEmpty($metodo->tipo);
        $this->assertEquals($user->id, $metodo->user_id);
    }
}
