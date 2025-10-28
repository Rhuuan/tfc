<?php

namespace Tests\Unit;

use App\Filament\Resources\ProjetoResource\Pages\GerarRecomendacaoProjeto;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GerarRecomendacaoProjetoTest extends TestCase
{
    public function test_gerar_recomendacao_successfully_extracts_mermaid_and_justificacao(): void
    {
        config([
            'services.gemini.url' => 'https://example.com/gemini',
            'services.gemini.key' => 'fake-key',
        ]);

        $mermaidBlock = <<<MARKDOWN
## Código Mermaid
```mermaid
graph TD;
A-->B
```

## Justificação
- Fluxo simples
MARKDOWN;

        Http::fake([
            'https://example.com/gemini?key=fake-key' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $mermaidBlock],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $page = app(GerarRecomendacaoProjeto::class);
        $page->dadosProjeto = ['id' => 1, 'nome' => 'Projeto Teste'];
        $page->contexto = json_encode($page->dadosProjeto);

    $page->gerarRecomendacao();

    $normalizedMermaid = str_replace("\r\n", "\n", $page->codigoMermaid ?? '');

    $this->assertSame("graph TD;\nA-->B", $normalizedMermaid);
        $this->assertSame('- Fluxo simples', $page->justificacao);
    }
}
