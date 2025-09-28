<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Models\Projeto;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GerarRecomendacaoProjeto extends Page
{
    protected static string $resource = ProjetoResource::class;
    protected static string $view = 'filament.resources.projeto-resource.pages.gerar-recomendacao-projeto';

    public ?array $dadosProjeto = [];
    public string $contexto = '{}';
    public ?string $resposta = null;

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
            // Aumentado timeout para 60s, pois a IA pode demorar
            $response = Http::withOptions([
                'verify' => config('app.env') !== 'local', // Mais seguro: verifica SSL em produção
            ])->timeout(60)->post(
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
                // Lança uma exceção com mais detalhes do erro da API
                throw new Exception('Erro na API do Gemini: ' . $response->status() . ' - ' . $response->body());
            }

            $data = $response->json();
            
            // Opcional: Para depurar, grave a resposta completa da API nos logs do Laravel
            // Log::info('Resposta da API Gemini:', $data);

            // Acessa a estrutura correta da resposta da API Gemini
            $this->resposta = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            // Se, por algum motivo, a estrutura da resposta for inesperada ou o texto vier vazio
            if (empty($this->resposta)) {
                 throw new Exception('A resposta da API não continha o texto esperado. Resposta recebida: ' . json_encode($data));
            }
            
            Notification::make()
                ->title('Recomendação gerada com sucesso')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Erro ao gerar recomendação')
                ->body($e->getMessage()) // Mostra a mensagem de erro detalhada
                ->danger()
                ->persistent()
                ->send();
        }
    }
}