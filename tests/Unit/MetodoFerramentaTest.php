<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\MetodoFerramenta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetodoFerramentaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um MetodoFerramenta pode ser criado com user_id automático.
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
     * Testa o relacionamento com User
     */
    public function test_relacionamento_user(): void
    {
        $user = User::factory()->create();
        $metodo = MetodoFerramenta::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $metodo->user);
        $this->assertEquals($user->id, $metodo->user->id);
    }

    /**
     * Testa campos obrigatórios
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
