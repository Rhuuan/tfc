<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Filament\Resources\ProjetoResource\Widgets\ProjetoRecomendacaoAtual;
use App\Models\Recomendacao;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewProjeto extends ViewRecord
{
    protected static string $resource = ProjetoResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        // Se já existir uma recomendação para este projeto, redireciona para a tela de resultado (Estado 3)
        $temRecomendacao = Recomendacao::where('projeto_id', $this->record->id)->exists();
        if ($temRecomendacao) {
            $this->redirectRoute('filament.admin.resources.projetos.gerarRecomendacao', [
                'record' => $this->record->id,
                'mostrarSalva' => 1,
            ]);
            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // Botão Gerar Recomendação já existente
            Action::make('gerarRecomendacao')
                ->label('Gerar Recomendação')
                ->color('warning')
                ->url(route('filament.admin.resources.projetos.gerarRecomendacao', ['record' => $this->record->id]))
                ->icon('heroicon-o-light-bulb')
                ->outlined(),

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

    protected function getHeaderWidgets(): array
    {
        return [
            ProjetoRecomendacaoAtual::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            ProjetoRecomendacaoAtual::class,
        ];
    }
}
