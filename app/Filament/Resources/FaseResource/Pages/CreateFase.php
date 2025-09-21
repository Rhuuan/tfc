<?php

namespace App\Filament\Resources\FaseResource\Pages;

use App\Filament\Resources\FaseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateFase extends CreateRecord
{
    protected static string $resource = FaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
    }
}
