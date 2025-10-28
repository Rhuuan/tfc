<x-filament::page>
    <div class="max-w-6xl mx-auto px-6 md:px-8 space-y-14">

        {{-- ======================================================= --}}
        {{-- ESTADO 1: TELA INICIAL --}}
        {{-- ======================================================= --}}
    <div wire:loading.remove wire:target="gerarRecomendacao" class="space-y-12">
            @if (!$resposta)
                <x-filament::section style="margin-bottom: 14px;">
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-briefcase class="w-6 h-6 text-primary-600" />
                            <span class="text-lg font-semibold text-primary-800">Informações do Projeto</span>
                        </div>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-800">
                        <div><span class="font-semibold">Projeto:</span> {{ $dadosProjeto['nome'] ?? '-' }}</div>
                        <div><span class="font-semibold">Criado em:</span> {{ isset($dadosProjeto['created_at']) ? \Carbon\Carbon::parse($dadosProjeto['created_at'])->format('d/m/Y H:i') : '-' }}</div>
                        <div class="md:col-span-2"><span class="font-semibold">Descrição:</span> {{ $dadosProjeto['descricao'] ?? '-' }}</div>
                    </div>
                </x-filament::section>

                <div class="p-8 bg-white rounded-xl shadow-md border border-gray-200 space-y-5">
                    <div class="flex flex-col gap-2" style="padding: 20px;">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <x-heroicon-o-light-bulb class="w-7 h-7 text-primary-500" />
                            Gerar Recomendação com IA
                        </h2>
                        <p class="text-sm text-gray-600">
                            O sistema analisará todo o contexto do projeto para gerar um fluxo de trabalho otimizado e uma justificativa técnica.
                        </p>
                    </div>

                    {{-- JSON técnico com destaque visual --}}
                    <div x-data="{ open: false }" class="text-sm" style="padding: 20px;">
                        <button @click="open = !open" class="text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1">
                            <span x-show="!open">Mostrar contexto técnico (JSON)</span>
                            <span x-show="open">Esconder contexto técnico</span>
                            <x-heroicon-s-chevron-down class="w-4 h-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                        <div
                            x-show="open"
                            x-transition
                            class="mt-3 bg-primary-950 text-primary-100 p-5 rounded-lg border border-primary-800/60 overflow-auto max-h-[260px] font-mono text-xs whitespace-pre-wrap shadow-inner"
                        >
                            {{ $contexto }}
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-filament::button wire:click="gerarRecomendacao" color="primary" size="xl">
                            <x-heroicon-s-sparkles class="w-5 h-5 mr-2"/>
                            Analisar e Gerar Fluxo
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </div>

        {{-- ======================================================= --}}
        {{-- ESTADO 2: CARREGAMENTO --}}
        {{-- ======================================================= --}}
        <div wire:loading.flex wire:target="gerarRecomendacao" class="w-full flex-col items-center justify-center text-center py-10 space-y-4">
            <x-filament::loading-indicator class="h-10 w-10 text-primary-500" />
            <h2 class="mt-4 text-lg font-semibold text-gray-700">Analisando o projeto e gerando recomendações...</h2>
            <p class="text-sm text-gray-500">A IA está processando as informações. Isso pode levar alguns segundos.</p>
            <div class="w-full max-w-4xl mt-8 space-y-6 animate-pulse">
                <div class="h-72 bg-gray-200 rounded-lg"></div>
                <div class="space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- ESTADO 3: RESULTADO (CORRIGIDO) --}}
        {{-- ======================================================= --}}
        @if ($resposta)
            <div 
                class="space-y-12"
                {{-- Ligamos o Alpine.js aqui, no container principal --}}
                x-data="{
                    codigo: @entangle('codigoMermaid'),
                    copiado: false,
                    async copiarCodigo() {
                        if (!this.codigo) {
                            return;
                        }

                        try {
                            await navigator.clipboard.writeText(this.codigo);
                            this.copiado = true;
                        } catch (error) {
                            console.error('Não foi possível copiar o código Mermaid.', error);
                        }

                        setTimeout(() => (this.copiado = false), 2500);
                    }
                }"
                {{-- E mandamos ele "assistir" a variável 'codigo' --}}
                x-init="$nextTick(() => window.renderMermaid(codigo))"
                x-effect="window.renderMermaid(codigo)"
                data-mermaid-container
            >
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-7 h-7 text-primary-500 mb-10" />
                            Recomendação Gerada
                        </h2>
                        <p class="text-base text-gray-600 mb-4">Abaixo está o fluxo de trabalho otimizado e a justificativa detalhada sugerida pela IA.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">
                    
                    {{-- COLUNA 1: FLUXO RECOMENDADO (DIAGRAMA) --}}
                    <x-filament::section>
                        <x-slot name="heading" >Fluxo Recomendado</x-slot>

                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                <p class="text-sm text-gray-600 lg:max-w-xl">
                                    Utilize o diagrama abaixo para visualizar a sequência recomendada e copie o código Mermaid para ajustar ou compartilhar.
                                </p>
                                <div class="flex items-center gap-2">
                                    <span
                                        x-show="copiado"
                                        x-transition
                                        class="text-xs font-semibold text-success-500"
                                    >Copiado!</span>
                                    <x-filament::button
                                        type="button"
                                        color="primary"
                                        size="sm"
                                        wire:loading.attr="disabled"
                                        wire:target="gerarRecomendacao"
                                        x-on:click="copiarCodigo"
                                        x-bind:disabled="!codigo"
                                        icon="heroicon-o-clipboard-document"
                                        icon-position="before"
                                        class="shadow-sm"
                                    >Copiar Mermaid</x-filament::button>
                                </div>
                            </div>
                        
                            {{-- O container onde o Mermaid vai desenhar --}}
                            {{-- Damos uma altura fixa (h-[600px]) e overflow-hidden --}}
                            <div class="bg-gray-900 rounded-lg w-full overflow-hidden border border-gray-700 flex-1 shadow-md" style="height: 540px;">
                                {{-- O SVG será injetado aqui e controlado pela biblioteca de pan-zoom --}}
                                <div id="mermaid-diagram" class="w-full h-full">
                                    <p class="text-gray-400 p-4 text-sm">O diagrama aparecerá aqui...</p>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    {{-- COLUNA 2: JUSTIFICAÇÃO (O seu código original, que está ótimo) --}}
                    <div class="rounded-xl border border-primary-700/70 bg-primary-600 p-8 lg:p-9 text-white shadow-lg space-y-5 max-h-[540px] overflow-auto">
                        <div class="text-lg font-semibold flex items-center gap-2 tracking-wide">
                            <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                            Justificação
                        </div>

                        <div class="prose prose-sm max-w-none prose-invert text-white
                            [&_*]:text-white
                            [&_strong]:text-white
                            [&_b]:text-white
                            [&_p]:text-white
                            [&_li]:text-white
                            [&_span]:text-white
                            [&_a]:text-white
                            [&_code]:text-white
                            [&_pre]:text-white
                            [&_thead_th]:text-white
                            [&_tbody_td]:text-white
                            [&_li::marker]:text-white
                            [&_h1]:text-white
                            [&_h2]:text-white
                            [&_h3]:text-white
                            [&_h4]:text-white
                            [&_h5]:text-white
                            [&_h6]:text-white
                            [&_em]:text-white
                        ">
                            {!! \Illuminate\Support\Str::markdown($justificacao) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 
                IMPORTANTE: O @push('scripts') que estava aqui dentro 
                foi removido para evitar duplicidade.
            --}}
        @endif

    </div>
</x-filament::page>

@push('scripts')
{{-- Carrega a biblioteca de Pan/Zoom PRIMEIRO --}}
<script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>

{{-- Carrega nosso script de módulo DEPOIS --}}
<script type="module">
    // 1. Importa o Mermaid
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';

    // 2. Inicializa o Mermaid
    mermaid.initialize({ 
        startOnLoad: false, 
        theme: 'dark' 
    });

    // 3. Define nossa função de renderização no 'window'
    window.renderMermaid = async function(content) {
        const el = document.getElementById('mermaid-diagram');
        if (!el || !content) {
            if (el) el.innerHTML = '<p class="text-gray-400 p-4">O diagrama aparecerá aqui...</p>';
            return;
        }

        // Limpa a instância antiga de pan-zoom, se existir
        if (window.panZoomInstance) {
            window.panZoomInstance.destroy();
            window.panZoomInstance = null;
        }

        const graphId = 'g' + new Date().getTime();
        
        try {
            // 4. Renderiza o SVG do Mermaid
            const { svg } = await mermaid.render(graphId, content);
            el.innerHTML = svg;

            // 5. ----> INICIA O PAN-ZOOM <----
            const svgElement = el.querySelector('svg');
            
            // Verifica se o SVG e a biblioteca (svgPanZoom) existem
            if (svgElement && window.svgPanZoom) { 
                
                // ==============================================================
                // AQUI ESTÁ A CORREÇÃO:
                // 1. Remove os atributos de tamanho fixo do Mermaid
                svgElement.removeAttribute('width');
                svgElement.removeAttribute('height');

                // 2. Força o SVG a preencher o container (para o pan-zoom funcionar)
                svgElement.style.width = '100%';
                svgElement.style.height = '1200px';
                
                // ==============================================================

                // 3. Atrasamos a inicialização em 1 tick (0ms) para
                // garantir que o browser renderizou o SVG antes do pan-zoom ler suas dimensões.
                setTimeout(() => {
                    // Inicia o pan-zoom
                    window.panZoomInstance = svgPanZoom(svgElement, {
                        zoomEnabled: true,
                        controlIconsEnabled: true, // Mostra botões de + / -
                        fit: true,                 // Ajusta o SVG ao centro
                        center: true,
                        minZoom: 0.1,              // Permite dar bastante zoom-out
                        maxZoom: 20                // Permite dar bastante zoom-in
                    });
                    
                    // Recalcula o tamanho ao redimensionar a janela
                    window.addEventListener('resize', () => {
                        if (window.panZoomInstance) {
                            
                        }
                    });
                }, 300); // 0ms timeout
            }
        } catch (e) {
            el.innerHTML = '<p class="text-red-400 p-4">Erro ao renderizar o diagrama: ' + e.message + '</p>';
            console.error(e);
        }
    }

    // 6. Adiciona o listener para o Alpine.js
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializa na carga da página (caso o código já exista)
        // Usamos um pequeno timeout para garantir que o Alpine também já carregou
                        setTimeout(() => {
                            const el = document.querySelector('[data-mermaid-container]');
                            if (el && el.__x) {
                                window.renderMermaid(el.__x.data.codigo);
                            }
                        }, 150);
    });
</script>
@endpush