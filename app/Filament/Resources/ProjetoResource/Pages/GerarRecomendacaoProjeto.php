<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Models\Projeto;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class GerarRecomendacaoProjeto extends Page
{
    protected static string $resource = ProjetoResource::class;

    protected static string $view = 'filament.resources.projeto-resource.pages.gerar-recomendacao-projeto';

    public Projeto $record;

    protected static ?string $title = 'Gerar Recomendação';

    public function mount($record): void
    {
        $this->record = Projeto::findOrFail($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->url(route('filament.admin.resources.projetos.view', ['record' => $this->record->id]))
        ];
    }
}
