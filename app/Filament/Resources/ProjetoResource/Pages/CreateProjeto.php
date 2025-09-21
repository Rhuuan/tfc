<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateProjeto extends CreateRecord
{
    protected static string $resource = ProjetoResource::class;

    /**
     * 🔹 Sempre salvar o registro vinculado ao usuário logado
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
    }
}
