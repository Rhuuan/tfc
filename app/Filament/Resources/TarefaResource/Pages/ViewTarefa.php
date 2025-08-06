<?php

namespace App\Filament\Resources\TarefaResource\Pages;

use App\Filament\Resources\TarefaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTarefa extends ViewRecord
{
    protected static string $resource = TarefaResource::class;

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
