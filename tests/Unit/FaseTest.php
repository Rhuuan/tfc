<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Atividade;
use App\Models\Fase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo possui as traits necessárias.
     */
    public function test_fase_model_has_required_traits(): void
    {
        $fase = new Fase();
        $this->assertContains(HasFactory::class, class_uses($fase));
    }

    /**
     * Testa se os atributos fillable estão configurados corretamente.
     */
    public function test_fase_model_has_correct_fillable_attributes(): void
    {
        $fase = new Fase();
        $expectedFillable = ['nome', 'data', 'user_id'];
        
        $this->assertEquals($expectedFillable, $fase->getFillable());
    }

    /**
     * Testa se o modelo usa a tabela correta.
     */
    public function test_fase_model_uses_correct_table(): void
    {
        $fase = new Fase();
        $this->assertEquals('fases', $fase->getTable());
    }

    /**
     * Testa se a chave primária está configurada corretamente.
     */
    public function test_fase_model_has_correct_primary_key(): void
    {
        $fase = new Fase();
        $this->assertEquals('id', $fase->getKeyName());
    }

    /**
     * Testa se é possível criar uma fase com dados válidos.
     */
    public function test_can_create_fase_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $faseData = [
            'nome' => 'Fase de Planejamento',
            'data' => '2025-12-31',
            'user_id' => $user->id,
        ];

        $fase = Fase::create($faseData);

        $this->assertInstanceOf(Fase::class, $fase);
        $this->assertEquals('Fase de Planejamento', $fase->nome);
        $this->assertEquals('2025-12-31', $fase->data);
        $this->assertEquals($user->id, $fase->user_id);
        $this->assertDatabaseHas('fases', $faseData);
    }

    /**
     * Testa se é possível usar a factory da fase.
     */
    public function test_can_use_fase_factory(): void
    {
        $fase = Fase::factory()->create();

        $this->assertInstanceOf(Fase::class, $fase);
        $this->assertNotEmpty($fase->nome);
        $this->assertNotEmpty($fase->data);
        $this->assertNotNull($fase->user_id);
        $this->assertDatabaseHas('fases', ['id' => $fase->id]);
    }

    /**
     * Testa se é possível criar múltiplas fases com factory.
     */
    public function test_can_create_multiple_fases_with_factory(): void
    {
        $fases = Fase::factory()->count(3)->create();

        $this->assertCount(3, $fases);
        $this->assertEquals(3, Fase::count());
        
        foreach ($fases as $fase) {
            $this->assertInstanceOf(Fase::class, $fase);
        }
    }

    /**
     * Testa se campos obrigatórios não podem ser nulos.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Fase::create([
            'nome' => null,
            'data' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar uma fase.
     */
    public function test_can_update_fase(): void
    {
        $fase = Fase::factory()->create();
        $novoNome = 'Fase Atualizada';
        
        $fase->update(['nome' => $novoNome]);
        
        $this->assertEquals($novoNome, $fase->fresh()->nome);
        $this->assertDatabaseHas('fases', [
            'id' => $fase->id,
            'nome' => $novoNome,
        ]);
    }

    /**
     * Testa se é possível deletar uma fase.
     */
    public function test_can_delete_fase(): void
    {
        $fase = Fase::factory()->create();
        $faseId = $fase->id;
        
        $fase->delete();
        
        $this->assertDatabaseMissing('fases', ['id' => $faseId]);
        $this->assertEquals(0, Fase::count());
    }

    /**
     * Testa o relacionamento com User.
     */
    public function test_fase_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $fase = Fase::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $fase->user);
        $this->assertEquals($user->id, $fase->user->id);
        $this->assertEquals($user->name, $fase->user->name);
    }

    /**
     * Testa o relacionamento many-to-many com Atividades.
     */
    public function test_fase_has_many_to_many_relationship_with_atividades(): void
    {
        $fase = Fase::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $fase->user_id]);

        $fase->atividades()->attach($atividade);

        $this->assertTrue($fase->atividades->contains($atividade));
        $this->assertInstanceOf(Atividade::class, $fase->atividades->first());
        $this->assertEquals(1, $fase->atividades->count());
    }

    /**
     * Testa se uma fase pode ter múltiplas atividades.
     */
    public function test_fase_can_have_multiple_atividades(): void
    {
        $fase = Fase::factory()->create();
        $atividades = Atividade::factory()->count(3)->create(['user_id' => $fase->user_id]);

        foreach ($atividades as $atividade) {
            $fase->atividades()->attach($atividade);
        }

        $this->assertEquals(3, $fase->atividades->count());
        
        foreach ($atividades as $atividade) {
            $this->assertTrue($fase->atividades->contains($atividade));
        }
    }

    /**
     * Testa se é possível desassociar atividades de uma fase.
     */
    public function test_can_detach_atividades_from_fase(): void
    {
        $fase = Fase::factory()->create();
        $atividade = Atividade::factory()->create(['user_id' => $fase->user_id]);

        // Associar primeiro
        $fase->atividades()->attach($atividade);
        $this->assertEquals(1, $fase->atividades->count());

        // Desassociar depois
        $fase->atividades()->detach($atividade);
        $this->assertEquals(0, $fase->fresh()->atividades->count());
    }

    /**
     * Testa a validação de formato de data se aplicável.
     */
    public function test_fase_data_field_accepts_valid_date(): void
    {
        $user = User::factory()->create();
        
        $fase = Fase::create([
            'nome' => 'Teste Data',
            'data' => '2025-12-31',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('2025-12-31', $fase->data);
    }

    /**
     * Testa se user_id é obrigatório ao criar uma fase.
     */
    public function test_user_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Fase::create([
            'nome' => 'Fase sem usuário',
            'data' => '2025-12-31',
            // user_id está faltando
        ]);
    }
}
