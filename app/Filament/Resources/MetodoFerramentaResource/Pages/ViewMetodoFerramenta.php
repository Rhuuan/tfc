<?php

namespace App\Filament\Resources\MetodoFerramentaResource\Pages;

use App\Filament\Resources\MetodoFerramentaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\URL;

class ViewMetodoFerramenta extends ViewRecord
{
    protected static string $resource = MetodoFerramentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => url()->previous()) // <-- resolve a URL dinamicamente
                ->openUrlInNewTab(false),
        ];
    } 
}