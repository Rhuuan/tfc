<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewProjeto extends ViewRecord
{
    protected static string $resource = ProjetoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botão Gerar Recomendação já existente
            Action::make('gerarRecomendacao')
                ->label('Gerar Recomendação')
                ->icon('heroicon-o-light-bulb')
                ->color('warning')
                ->action(fn () => $this->gerarRecomendacao()),

            // ✅ Novo botão: Voltar para Início
            Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(url()->previous())
                ->openUrlInNewTab(false),
        ];
    }

    // Aqui fica a lógica da recomendação
    protected function gerarRecomendacao(): void
    {
        // Coloque a lógica real depois
        $this->notify('success', 'Recomendação gerada com sucesso!');
    }
}
