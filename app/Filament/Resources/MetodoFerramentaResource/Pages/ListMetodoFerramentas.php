<?php

namespace App\Filament\Resources\MetodoFerramentaResource\Pages;

use App\Filament\Resources\MetodoFerramentaResource;
use App\Filament\Resources\MetodoFerramentaResource\Widgets\MetodoFerramentaIntro;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMetodoFerramentas extends ListRecords
{
    protected static string $resource = MetodoFerramentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MetodoFerramentaIntro::class,
        ];
    }
    
}
