<?php

namespace App\Filament\Resources\ProjetoResource\Widgets;

use App\Models\Projeto;
use App\Models\Recomendacao;
use Filament\Widgets\Widget;

class ProjetoRecomendacaoAtual extends Widget
{
    protected static string $view = 'filament.resources.projeto-resource.widgets.projeto-recomendacao-atual';

    public ?int $projetoId = null;
    public ?string $codigoMermaid = null;
    public ?string $justificacao = null;

    public function mount(): void
    {
        $this->projetoId = (int) (request()->route('record') ?? 0);
        if (!$this->projetoId) {
            return;
        }

        $reco = Recomendacao::where('projeto_id', $this->projetoId)
            ->latest('id')
            ->first();

        if ($reco) {
            $this->codigoMermaid = $reco->codigo_mermaid;
            $this->justificacao = $reco->justificacao;
        }
    }
}
