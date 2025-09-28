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

    // Propriedades Públicas do Componente Livewire
    public ?array $dadosProjeto = [];
    public string $contexto = '{}';
    public ?string $resposta = null;
    
    public ?string $codigoMermaid = null;
    public ?string $justificacao = null;

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
            'codigoMermaid' => $this->codigoMermaid,
            'justificacao' => $this->justificacao,
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

    // =======================================================
    // MÉTODO PARA O BOTÃO "GERAR OUTRA RECOMENDAÇÃO"
    // =======================================================
    /*
    public function resetarPagina(): void
    {
        $this->resposta = null;
        $this->codigoMermaid = null;
        $this->justificacao = null;
    }
    */
    
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
        
        $prompt = <<<EOT
Você é um consultor Sênior de Gestão de Projetos, especialista em otimizar fluxos de trabalho. Sua comunicação é clara, direta e focada em resultados práticos.

Analise o seguinte projeto em formato JSON.
**Importante: Analise o projeto mesmo que algumas seções como 'fases', 'atividades' ou 'tarefas' estejam vazias. Se informações cruciais estiverem faltando, sua primeira recomendação DEVE ser sobre a necessidade de detalhar melhor essas seções para um planejamento mais eficaz.**

Contexto do Projeto:
{$this->contexto}

Sua tarefa é gerar uma recomendação de otimização para este projeto.
A sua resposta DEVE seguir ESTRITAMENTE a seguinte estrutura, usando Markdown para formatação:

## Código Mermaid
Gere APENAS o código para um diagrama de fluxo (flowchart) em sintaxe Mermaid. O diagrama deve estar dentro de um bloco de código. Se não houver dados suficientes para criar um fluxo detalhado, crie um fluxo genérico de boas práticas para o tipo de projeto descrito. NÃO inclua nenhum texto explicativo NESTA seção, apenas o código.
É importante que traga graph TD para que o diagrama seja renderizado corretamente. O graph TD deve vim com os ; Assim graph TD;

```mermaid
graph TD:
    A[Início] --> B{Planejamento};
    B --> C[Execução];
    C --> D[Monitoramento];
    D --> E[Encerramento];
    Justificação
Explique em uma lista (bullet points) o porquê de cada etapa do fluxo que você criou acima. Seja direto e justifique como o novo fluxo resolve um problema ou otimiza o processo atual. Use poucas palavras.

NÃO adicione nenhuma outra informação ou texto fora da estrutura solicitada.
EOT;

    try {
        $response = Http::withOptions([
            'verify' => config('app.env') !== 'local',
        ])->timeout(60)->post(
            config('services.gemini.url') . '?key=' . config('services.gemini.key'),
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]
        );

        if ($response->failed()) {
            throw new Exception('Erro na API do Gemini: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        
        $this->resposta = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if (empty($this->resposta)) {
             throw new Exception('A resposta da API não continha o texto esperado. Resposta recebida: ' . json_encode($data));
        }

        preg_match('/```mermaid(.*?)```/s', $this->resposta, $mermaidMatches);
        $this->codigoMermaid = trim($mermaidMatches[1] ?? 'graph TD; A[Erro]; A-->B[Diagrama não encontrado na resposta da IA];');

        preg_match('/## Justificação\s*(.*)/s', $this->resposta, $justificacaoMatches);
        $this->justificacao = trim($justificacaoMatches[1] ?? 'Nenhuma justificação foi encontrada na resposta da IA.');
        
        Notification::make()
            ->title('Recomendação gerada com sucesso')
            ->success()
            ->send();

    } catch (Exception $e) {
        Notification::make()
            ->title('Erro ao gerar recomendação')
            ->body($e->getMessage())
            ->danger()
            ->persistent()
            ->send();
        }
    }
}