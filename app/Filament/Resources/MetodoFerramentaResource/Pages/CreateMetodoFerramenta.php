<?php

namespace App\Filament\Resources\MetodoFerramentaResource\Pages;

use App\Filament\Resources\MetodoFerramentaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateMetodoFerramenta extends CreateRecord
{
    protected static string $resource = MetodoFerramentaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
    }
}
