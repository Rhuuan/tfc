<?php

namespace App\Filament\Resources\AtividadeResource\Pages;

use App\Filament\Resources\AtividadeResource;
use App\Filament\Resources\AtividadeResource\Widgets\AtividadeIntro;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtividades extends ListRecords
{
    protected static string $resource = AtividadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AtividadeIntro::class,
        ];
    }
}
