<x-filament::page>
    <div class="max-w-6xl mx-auto px-6 md:px-8 space-y-10">

        {{-- ======================================================= --}}
        {{-- ESTADO 1: TELA INICIAL --}}
        {{-- ======================================================= --}}
        <div wire:loading.remove wire:target="gerarRecomendacao">
            @if (!$resposta)
                <x-filament::section>
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

                <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200 mt-10 space-y-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-light-bulb class="w-7 h-7 text-primary-500" />
                        Gerar Recomendação com IA
                    </h2>
                    <p class="text-sm text-gray-600">
                        O sistema analisará todo o contexto do projeto para gerar um fluxo de trabalho otimizado e uma justificação técnica.
                    </p>

                    {{-- JSON técnico com fundo preto e letras brancas --}}
                    <div x-data="{ open: false }" class="text-sm">
                        <button @click="open = !open" class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1">
                            <span x-show="!open">Mostrar contexto técnico (JSON)</span>
                            <span x-show="open">Esconder contexto técnico</span>
                            <x-heroicon-s-chevron-down class="w-4 h-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" x-transition class="mt-2 bg-black text-white p-4 rounded-md overflow-auto max-h-[250px] font-mono border text-xs whitespace-pre-wrap">
                            {{ $contexto }}
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
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
        {{-- ESTADO 3: RESULTADO --}}
        {{-- ======================================================= --}}
        @if ($resposta)
            <div class="space-y-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-7 h-7 text-primary-500" />
                            Recomendação Gerada
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Abaixo está o fluxo de trabalho otimizado e a justificação.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Mermaid com fundo preto e texto branco --}}
                    <x-filament::section>
                        <x-slot name="heading">Código Mermaid</x-slot>
                        <div class="p-4 bg-black text-white rounded-md min-h-[200px] flex items-center justify-center overflow-auto">
                            <pre class="mermaid whitespace-pre-wrap">{{ trim($codigoMermaid) }}</pre>
                        </div>
                    </x-filament::section>

                    {{-- Justificação corrigida com texto branco --}}
                    <div class="rounded-xl border border-primary-800 bg-primary-600 p-6 text-white shadow-sm space-y-4 max-h-[400px] overflow-auto">
                        <div class="text-lg font-semibold flex items-center gap-2">
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

            {{-- Scripts Mermaid --}}
            @push('scripts')
                <script type="module">
                    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.js';
                    mermaid.initialize({ startOnLoad: true, theme: 'default' });

                    document.addEventListener('livewire:load', function () {
                        Livewire.hook('message.processed', () => {
                            if (document.querySelector('.mermaid')) {
                                document.querySelectorAll('.mermaid').forEach(el => {
                                    el.removeAttribute('data-processed');
                                });
                                mermaid.run();
                            }
                        });
                    });
                </script>
                <style>
                    .mermaid text {
                        fill: #FFFFFF !important;
                    }
                    .mermaid path.flowchart-link {
                        stroke: #cccccc !important;
                    }
                </style>
            @endpush
        @endif

    </div>
</x-filament::page>
