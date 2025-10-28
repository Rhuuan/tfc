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
        
        // Extrai o nome e a descrição do projeto para usar no prompt
        // Extrai o nome e a descrição do projeto para usar no prompt
        $projetoNome = $this->dadosProjeto['nome'] ?? 'Nome não definido';
        $projetoDescricao = $this->dadosProjeto['descricao'] ?? 'Descrição não definida';

        $prompt = <<<EOT
Você é um Consultor Sênior de Engenharia de Software e Gestão de Projetos.

Sua tarefa é analisar o JSON de um projeto e gerar duas coisas: (1) um código Mermaid e (2) uma justificação.

--------------------------------------------------
PROJETO PARA ANALISAR:
- **Nome:** {$projetoNome}
- **Descrição:** {$projetoDescricao}
- **Estrutura JSON (Fases, Atividades, Tarefas já cadastradas):**
{$this->contexto}
--------------------------------------------------

REGRAS DA SUA RESPOSTA:
Sua resposta DEVE seguir ESTRITAMENTE o formato Markdown abaixo.

## Código Mermaid
Gere APENAS o código para um diagrama de fluxo `graph TD;`.
- Use `subgraph` para a hierarquia (Fase > Atividade > Tarefa).
- **CRÍTICO:** Use IDs únicos (sem espaços, ex: `F1`) e nomes em aspas (ex: `F1["Fase: Planejamento"]`).
- **CRÍTICO:** `subgraph`, `end`, e comentários `%%` DEVEM estar em linhas separadas.
- Integre os itens do JSON acima e adicione (sugira) as etapas que faltam para um fluxo de software completo.

```mermaid
graph TD;
    %% Inicie seu código aqui.
    %% Exemplo de sintaxe CORRETA:
    
    subgraph F1_Exemplo["Fase Exemplo 1 (Sua Sugestão)"]
        direction TB
        A1_1["Atividade 1.1 (Registrada)"] --> T1_1_1["Tarefa 1.1.1 (Registrada)"]
    end
    
    subgraph F2_Exemplo["Fase Exemplo 2 (Sua Sugestão)"]
        direction TB
        A2_1["Atividade 2.1 (Sua Sugestão)"]
    end
    
    %% Conexão
    F1_Exemplo --> F2_Exemplo
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

        
        // PARSING DA JUSTIFICAÇÃO (CORRIGIDO)
        // Procura por "## Justificação" (ou "### Justificação", etc.) e captura tudo depois
        preg_match('/(#{2,}\s*Justificação)([\s\S]*)/is', $this->resposta, $justificacaoMatches);
        
        if (isset($justificacaoMatches[2]) && !empty(trim($justificacaoMatches[2]))) {
             $this->justificacao = trim($justificacaoMatches[2]);
        } else {
             // Fallback: Se não encontrar o TÍTULO, talvez a IA tenha retornado a justificação
             // logo após o bloco mermaid. Vamos tentar pegar o que vem DEPOIS.
             $parts = preg_split('/```/s', $this->resposta);
             if (isset($parts[2]) && !empty(trim($parts[2]))) {
                // Pega a terceira parte (depois do fechamento do ```) e limpa
                // CORRIGIDO AQUI:
                $this->justificacao = trim(preg_replace('/^justificação/i', '', $parts[2]));
             } else {
                // Se tudo falhar, usa a mensagem de erro
                // CORRIGIDO AQUI:
                $this->justificacao = 'Nenhuma justificação foi encontrada na resposta da IA.';
             }
        }
        
        // Se a justificação ainda for a mensagem de erro, vamos logar a resposta crua para debug
        // CORRIGIDO AQUI:
        if ($this->justificacao === 'Nenhuma justificação foi encontrada na resposta da IA.') {
            // CORRIGIDO AQUI: (e adicionado Log::)
            \Illuminate\Support\Facades\Log::warning('Falha ao extrair justificação. Resposta da IA: ' . $this->resposta);
        }


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