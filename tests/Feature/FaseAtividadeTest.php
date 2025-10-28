<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Fase;
use App\Models\Atividade;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FaseAtividadeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_criar_fase_com_atividades()
    {
    $fase = Fase::factory()->create();
    $atividade = Atividade::factory()->create();

    $atividade->fase()->associate($fase);
    $atividade->save();

    $this->assertEquals($fase->id, $atividade->fresh()->fase->id);
    }
}
