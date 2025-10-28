<?php

namespace App\Filament\Resources\FaseResource\Pages;

use App\Filament\Resources\FaseResource;
use App\Filament\Resources\FaseResource\Widgets\FaseIntro;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFases extends ListRecords
{
    protected static string $resource = FaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FaseIntro::class,
        ];
    }
}
