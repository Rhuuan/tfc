<?php

namespace App\Filament\Resources\FaseResource\Pages;

use App\Filament\Resources\FaseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewFase extends ViewRecord
{
    protected static string $resource = FaseResource::class;

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
