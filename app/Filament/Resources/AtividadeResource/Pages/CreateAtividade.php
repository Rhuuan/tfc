<?php

namespace App\Filament\Resources\AtividadeResource\Pages;

use App\Filament\Resources\AtividadeResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateAtividade extends CreateRecord
{
    protected static string $resource = AtividadeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
    }
}
