<?php

namespace App\Filament\Resources\AtividadeResource\Pages;

use App\Filament\Resources\AtividadeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtividade extends EditRecord
{
    protected static string $resource = AtividadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
