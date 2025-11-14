<?php

namespace App\Filament\Resources\ProjetoResource\Pages;

use App\Filament\Resources\ProjetoResource;
use App\Models\Projeto;
use App\Models\Recomendacao;
use App\Models\RecomendacaoFeedback;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
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
    public ?int $recomendacaoId = null;
    public ?string $userFeedback = null; // 'like' | 'dislike' | null

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

            // Se veio indicado para mostrar a última recomendação salva,
            // carregamos a mais recente e setamos os campos para renderizar o Estado 3 imediatamente
            $mostrarSalva = (bool) request()->boolean('mostrarSalva');
            if ($mostrarSalva) {
                $ultima = Recomendacao::where('projeto_id', $this->record)
                    ->latest('id')
                    ->first();

                if ($ultima) {
                    $this->recomendacaoId = $ultima->id;
                    $this->codigoMermaid = $ultima->codigo_mermaid;
                    $this->justificacao = $ultima->justificacao;
                    // Qualquer valor não-nulo ativa o Estado 3 na view; usamos a resposta bruta, se houver
                    $this->resposta = $ultima->resposta_bruta ?? 'Recomendação carregada do histórico.';

                    // Carrega feedback do usuário, se houver
                    $uid = \Filament\Facades\Filament::auth()->id() ?? Auth::id();
                    if ($uid) {
                        $fb = RecomendacaoFeedback::where('recomendacao_id', $ultima->id)
                            ->where('user_id', $uid)
                            ->first();
                        $this->userFeedback = $fb->value ?? null;
                    }
                }
            }
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
            'userFeedback' => $this->userFeedback,
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

        // Construção de preferências derivadas de feedbacks do usuário (sem copiar conteúdo)
        $fewShot = $this->montarPreferenciasDoUsuario();
        // Checklist obrigatório de todos os itens cadastrados (deve constar ou ser justificado)
        $checklist = $this->montarChecklistObrigatorio();

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

PRINCÍPIOS OBRIGATÓRIOS:
- Use EXCLUSIVAMENTE as informações do projeto fornecidas no JSON acima (fases/atividades/tarefas cadastradas) como base factual.
- Você PODE sugerir etapas que FALTAM apenas para completar o fluxo, mas SEM inventar detalhes que contradigam o projeto.
- Preserve exatamente os nomes do projeto/itens fornecidos no JSON (não renomeie itens existentes; apenas adicione onde fizer sentido).
- Não copie nem reaproveite conteúdo literal de quaisquer exemplos; eles servem apenas como referência de estilo/estrutura.
- Em caso de conflito entre exemplos e o JSON do projeto, o JSON do projeto sempre prevalece.
- Cada FASE, ATIVIDADE, TAREFA e MÉTODO/FERRAMENTA cadastrada DEVE aparecer no diagrama. Se optar por omitir um item, inclua-o na seção "Itens Omitidos e Justificativa" explicando claramente o motivo e o impacto.

{$fewShot}

COBERTURA OBRIGATÓRIA DOS ITENS CADASTRADOS (todos devem estar presentes ou justificados):
{$checklist}

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

        // PARSING DA JUSTIFICATIVA/ JUSTIFICAÇÃO (mais robusto)
        // 1) Remover o bloco mermaid do texto bruto para evitar confundir o parse da seção
        $respostaSemMermaid = preg_replace('/```mermaid[\s\S]*?```/i', '', (string) $this->resposta);
        // 2) Tentar por cabeçalho com diferentes grafias: Justificação ou Justificativa
        $padraoTitulo = '/(#{2,}\s*(Justificação|Justificativa))([\s\S]*)/i';
        if (preg_match($padraoTitulo, $respostaSemMermaid, $m)) {
            $this->justificacao = trim($m[3]);
        } else {
            // 3) Fallback: pegar o que vem depois do bloco mermaid original
            $parts = preg_split('/```mermaid[\s\S]*?```/i', (string) $this->resposta);
            if (isset($parts[1]) && !empty(trim($parts[1]))) {
                $texto = trim($parts[1]);
                // Remover um possível prefixo "Justificação:" ou "Justificativa:"
                $texto = preg_replace('/^\s*(Justificação|Justificativa)\s*:?\s*/i', '', $texto);
                $this->justificacao = $texto;
            } else {
                $this->justificacao = 'Nenhuma justificativa foi encontrada na resposta da IA.';
            }
        }
        
        // Se a justificação ainda for a mensagem de erro, vamos logar a resposta crua para debug
        // CORRIGIDO AQUI:
        if (in_array($this->justificacao, ['Nenhuma justificação foi encontrada na resposta da IA.', 'Nenhuma justificativa foi encontrada na resposta da IA.'])) {
            \Illuminate\Support\Facades\Log::warning('Falha ao extrair justificativa. Resposta da IA: ' . $this->resposta);
        }

        // Verificação automática de cobertura antes de persistir
        $cobertura = $this->verificarCoberturaItens($this->codigoMermaid, (string) $this->justificacao);
        if (!empty($cobertura['faltando'])) {
            $listaFaltando = implode("\n", array_map(fn($i) => "- {$i}", $cobertura['faltando']));
            $anexo = "\n\n## Verificação Automática de Cobertura\nOs itens a seguir não foram encontrados explicitamente no diagrama e não parecem justificados pelo texto:\n{$listaFaltando}\nRevise ou gere nova recomendação para cobrir ou justificar.";
            $this->justificacao = trim((string) $this->justificacao) . $anexo;
        }


        // Persistir no banco a recomendação vinculada ao projeto
        try {
            $codigoMermaid = trim((string) ($this->codigoMermaid ?? ''));
            if ($codigoMermaid === '') {
                $codigoMermaid = 'graph TD; A[Erro]; A-->B[Diagrama não encontrado na resposta da IA];';
            }

            $justificacao = trim((string) ($this->justificacao ?? ''));

            // Salva via relação do projeto para garantir o vínculo correto
            $projeto = Projeto::find($this->record);
            if ($projeto) {
                $userId = \Filament\Facades\Filament::auth()->id() ?? Auth::id() ?? $projeto->user_id;
                $rec = $projeto->recomendacoes()->create([
                    'user_id'        => $userId,
                    'codigo_mermaid' => $codigoMermaid,
                    'justificacao'   => $justificacao !== '' ? $justificacao : null,
                    'resposta_bruta' => $this->resposta,
                ]);
                $this->recomendacaoId = $rec->id;
            } else {
                $userId = \Filament\Facades\Filament::auth()->id() ?? Auth::id();
                $rec = Recomendacao::create([
                    'projeto_id'     => $this->record,
                    'user_id'        => $userId,
                    'codigo_mermaid' => $codigoMermaid,
                    'justificacao'   => $justificacao !== '' ? $justificacao : null,
                    'resposta_bruta' => $this->resposta,
                ]);
                $this->recomendacaoId = $rec->id;
            }

            // Reset feedback visual até o usuário escolher novamente
            $this->userFeedback = null;
        } catch (\Throwable $t) {
            Log::error('Falha ao salvar recomendação: ' . $t->getMessage(), [
                'projeto_id' => $this->record,
                'codigo_mermaid_len' => isset($codigoMermaid) ? strlen($codigoMermaid) : null,
                'justificacao_len'   => isset($justificacao) ? strlen($justificacao) : null,
                'has_projeto'        => isset($projeto) && (bool) $projeto,
            ]);
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

    /**
     * Upsert de feedback do usuário (like/dislike). Clicar novamente no mesmo valor remove.
     */
    public function darFeedback(string $valor): void
    {
        if (!in_array($valor, ['like', 'dislike'], true)) {
            return;
        }
        if (!$this->recomendacaoId) {
            Notification::make()->title('Nada para avaliar')->body('Gere ou carregue uma recomendação primeiro.')->warning()->send();
            return;
        }
        $uid = \Filament\Facades\Filament::auth()->id() ?? Auth::id();
        if (!$uid) {
            Notification::make()->title('Usuário não autenticado')->danger()->send();
            return;
        }

        // Alterna se o mesmo valor já estiver selecionado
        $existente = RecomendacaoFeedback::where('recomendacao_id', $this->recomendacaoId)
            ->where('user_id', $uid)
            ->first();

        if ($existente && $existente->value === $valor) {
            $existente->delete();
            $this->userFeedback = null;
            // Força re-render do diagrama após mudança de feedback
            $this->dispatch('re-render-mermaid');
            Notification::make()->title('Feedback removido')->success()->send();
            return;
        }

        RecomendacaoFeedback::updateOrCreate(
            ['recomendacao_id' => $this->recomendacaoId, 'user_id' => $uid],
            ['value' => $valor]
        );
        $this->userFeedback = $valor;
        // Emite evento para re-render confiável do diagrama (evita sumir após diff parcial Livewire)
        $this->dispatch('re-render-mermaid');
    Notification::make()->title('Feedback registrado')->success()->send();
    }

    /**
     * Monta uma seção de exemplos preferidos pelo usuário (few-shot) a partir de recomendações curtidas.
     * Escopo: mesmo projeto e usuário, para evitar vazamento de contexto entre projetos.
     */
    protected function montarPreferenciasDoUsuario(): string
    {
        $uid = \Filament\Facades\Filament::auth()->id() ?? Auth::id();
        if (!$uid || !$this->record) {
            return '';
        }

        // Apenas sinaliza preferências de estilo/estrutura, sem incluir conteúdo de recomendações anteriores
        // Mantém dados do projeto isolados e evita vazamento entre execuções.
        $temLikes = Recomendacao::query()
            ->where('projeto_id', $this->record)
            ->whereHas('feedbacks', function ($q) use ($uid) {
                $q->where('user_id', $uid)->where('value', 'like');
            })
            ->exists();

        if (!$temLikes) {
            return '';
        }

        return <<<TXT
OBSERVAÇÃO SOBRE PREFERÊNCIAS DO USUÁRIO (estilo, não conteúdo):
- Diagramas claros com organização por Fase > Atividade > Tarefa (subgraphs) foram melhor avaliados.
- Conexões explícitas entre fases e direção consistente (por exemplo, top-to-bottom) são preferidas.
- Completar lacunas do fluxo com etapas genéricas e coerentes (sem contradizer o JSON) aumenta a utilidade.
- Manter nomes exatamente como no projeto e a hierarquia fiel é requisito.
- NÃO COPIE conteúdos de resultados anteriores; use estas preferências apenas como guia de estilo.

TXT;
    }
    /**
     * Monta checklist de todos os itens cadastrados para reforçar cobertura no prompt.
     */
    protected function montarChecklistObrigatorio(): string
    {
        $fases = $this->dadosProjeto['fases'] ?? [];
        $atividades = $this->dadosProjeto['atividades'] ?? [];
        $tarefas = $this->dadosProjeto['tarefas'] ?? [];
        $metodos = $this->dadosProjeto['metodo_ferramentas'] ?? ($this->dadosProjeto['metodoFerramentas'] ?? []);

        $linhas = [];
        foreach ($fases as $f) {
            $linhas[] = "FASE: " . ($f['nome'] ?? '(sem nome)');
        }
        foreach ($atividades as $a) {
            $linhas[] = "ATIVIDADE: " . ($a['nome'] ?? '(sem nome)');
        }
        foreach ($tarefas as $t) {
            $linhas[] = "TAREFA: " . ($t['nome'] ?? '(sem nome)');
        }
        foreach ($metodos as $m) {
            $linhas[] = "METODO/FERRAMENTA: " . ($m['nome'] ?? '(sem nome)');
        }
        return implode("\n", $linhas);
    }

    /**
     * Verifica cobertura de itens no código mermaid e tentativa de justificativa.
     * Retorna ['faltando' => [...]] com itens não presentes por nome (case-insensitive).
     */
    protected function verificarCoberturaItens(string $mermaid, string $justificacao): array
    {
        $mermaidLower = mb_strtolower($mermaid);
        $justLower = mb_strtolower($justificacao);

        $faltando = [];
        $coletar = function(array $lista, string $prefixo) use (&$faltando, $mermaidLower, $justLower) {
            foreach ($lista as $item) {
                $nome = trim((string)($item['nome'] ?? ''));
                if ($nome === '') { continue; }
                $nomeLower = mb_strtolower($nome);
                $presente = str_contains($mermaidLower, $nomeLower);
                $justificado = str_contains($justLower, $nomeLower); // heurística simples
                if (!$presente && !$justificado) {
                    $faltando[] = $prefixo . $nome;
                }
            }
        };

        $coletar($this->dadosProjeto['fases'] ?? [], 'Fase: ');
        $coletar($this->dadosProjeto['atividades'] ?? [], 'Atividade: ');
        $coletar($this->dadosProjeto['tarefas'] ?? [], 'Tarefa: ');
        $metodos = $this->dadosProjeto['metodo_ferramentas'] ?? ($this->dadosProjeto['metodoFerramentas'] ?? []);
        $coletar($metodos, 'Metodo/Ferramenta: ');

        return ['faltando' => $faltando];
    }
}