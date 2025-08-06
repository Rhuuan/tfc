<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\ButtonAction;

class GerarRecomendacaoProjeto extends Page
{
    protected static string $resource = ProjetoResource::class;

    protected static string $view = 'filament.resources.projeto-resource.pages.gerar-recomendacao-projeto';

    public $record;

    public function mount($record): void
    {
        $this->record = ProjetoResource::getModel()::findOrFail($record);
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