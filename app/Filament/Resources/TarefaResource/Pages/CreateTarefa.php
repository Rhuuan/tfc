<?php

namespace App\Filament\Resources\TarefaResource\Pages;

use App\Filament\Resources\TarefaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateTarefa extends CreateRecord
{
    protected static string $resource = TarefaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
    }
}
