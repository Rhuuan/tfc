<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Fase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cria_fase_com_user_id()
    {
        $fase = Fase::factory()->create();
        $this->assertNotNull($fase->user_id);
    }

    /** @test */
    public function relacionamento_user()
    {
        $fase = Fase::factory()->create();
        $this->assertInstanceOf(User::class, $fase->user);
    }

    /** @test */
    public function campos_obrigatorios()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Fase::create([]); // deve falhar por não preencher 'nome', 'data' e 'user_id'
    }
}
