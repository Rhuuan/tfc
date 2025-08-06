<?php

namespace App\Filament\Resources\AtividadeResource\Pages;

use App\Filament\Resources\AtividadeResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewAtividade extends ViewRecord
{
    protected static string $resource = AtividadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(url()->previous())
                ->openUrlInNewTab(false),
        ];
    }  
}
