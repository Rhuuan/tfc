<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Models\Projeto;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class GerarRecomendacaoProjeto extends Page
{
    protected static string $resource = ProjetoResource::class;

    protected static string $view = 'filament.resources.projeto-resource.pages.gerar-recomendacao-projeto';

    protected Projeto $projeto;
    public string $contexto;

    public function mount(int|string $record): void
    {
        // Carrega o projeto com os relacionamentos necessários
        $this->projeto = ProjetoResource::getModel()::with([
            'fases',
            'atividades',
            'tarefas',
            'metodoFerramentas',
        ])->findOrFail($record);

        // Prepara o contexto como JSON formatado
        $this->contexto = json_encode($this->projeto->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->projeto->toArray(),
            'contexto' => $this->contexto,
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('Voltar')
                ->label('Voltar')
                ->button()
                ->color('gray')
                ->url($this->getResource()::getUrl())
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}