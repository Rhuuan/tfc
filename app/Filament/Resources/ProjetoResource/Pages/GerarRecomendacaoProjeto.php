<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Models\Projeto;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Exception;

class GerarRecomendacaoProjeto extends Page
{
    protected static string $resource = ProjetoResource::class;
    protected static string $view = 'filament.resources.projeto-resource.pages.gerar-recomendacao-projeto';

    public ?array $dadosProjeto = [];   // substitui $record
    public string $contexto = '{}';     // JSON separado
    public ?string $resposta = null;

    // Recebe o ID do projeto via query string (ex: ?record=1)
    public ?int $record = null;

    public function mount(): void
    {
        if (!$this->record) {
            Notification::make()
                ->title('Projeto não informado')
                ->body('Nenhum projeto foi especificado.')
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        try {
            $projeto = Projeto::with([
                'fases',
                'atividades',
                'tarefas',
                'metodoFerramentas',
            ])->findOrFail($this->record);

            $this->dadosProjeto = $projeto->toArray();
            $this->contexto = json_encode($this->dadosProjeto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            Notification::make()
                ->title('Projeto não carregado')
                ->body('O projeto não pôde ser carregado: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->dadosProjeto = [];
            $this->contexto = '{}';
        }
    }

    protected function getViewData(): array
    {
        return [
            'dadosProjeto' => $this->dadosProjeto,
            'contexto' => $this->contexto,
            'resposta' => $this->resposta,
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('Voltar')
                ->label('Voltar')
                ->button()
                ->color('gray')
                ->url($this->getResource()::getUrl())
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    public function gerarRecomendacao(): void
    {
        if (empty($this->dadosProjeto)) {
            Notification::make()
                ->title('Projeto não carregado')
                ->body('O projeto não pôde ser carregado.')
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        $faltas = [];
        if (empty($this->dadosProjeto['fases'])) $faltas[] = 'Fases';
        if (empty($this->dadosProjeto['atividades'])) $faltas[] = 'Atividades';
        if (empty($this->dadosProjeto['tarefas'])) $faltas[] = 'Tarefas';
        if (empty($this->dadosProjeto['metodo_ferramentas'])) $faltas[] = 'Métodos/Ferramentas';

        if (!empty($faltas)) {
            Notification::make()
                ->title('Informações incompletas')
                ->body('Faltando dados: ' . implode(', ', $faltas) . '.')
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        $prompt = <<<EOT
Você é um especialista em gestão de projetos.
Analise o seguinte contexto de projeto (em JSON) e gere:
1. Um diagrama em Mermaid mostrando o fluxo de trabalho ideal.
2. Um texto explicativo com recomendações e justificativas para cada etapa.

Contexto do Projeto:
{$this->contexto}
EOT;

        try {
            // Chamada HTTP com SSL desativado apenas para desenvolvimento local
            $response = Http::withOptions([
                'verify' => false
            ])->timeout(30)->post(
                config('services.gemini.url') . '?key=' . config('services.gemini.key'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->failed()) {
                throw new Exception('Erro ao processar requisição: ' . $response->body());
            }

            $data = $response->json();
            $this->resposta = $data['output'] ?? '[Sem resposta da LLM]';

            Notification::make()
                ->title('Recomendação gerada com sucesso')
                ->success()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title('Erro ao conectar com o serviço de recomendação')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
