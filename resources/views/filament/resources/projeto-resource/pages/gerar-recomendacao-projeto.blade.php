<x-filament::page>
    <div class="space-y-10 max-w-6xl mx-auto px-6 md:px-8">

        {{-- ===================================================================== --}}
        {{-- TUDO ABAIXO SÓ SERÁ MOSTRADO ANTES DE GERAR A RECOMENDAÇÃO --}}
        {{-- ===================================================================== --}}
        @if (!$resposta)
            {{-- Bloco de informações do projeto --}}
            <x-filament::section style="margin-bottom: 20px;">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-briefcase class="w-6 h-6 text-primary-600" />
                        <span class="text-lg font-semibold text-primary-800">Informações do Projeto</span>
                    </div>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-800 leading-relaxed">
                    <div>
                        <span class="font-semibold">Projeto:</span> {{ $dadosProjeto['nome'] ?? '-' }}
                    </div>
                    <div>
                        <span class="font-semibold">Criado em:</span>
                        {{ isset($dadosProjeto['created_at']) ? \Carbon\Carbon::parse($dadosProjeto['created_at'])->format('d/m/Y H:i') : '-' }}
                    </div>
                    <div class="md:col-span-2">
                        <span class="font-semibold">Descrição:</span> {{ $dadosProjeto['descricao'] ?? '-' }}
                    </div>
                </div>
            </x-filament::section>

            {{-- Todos os outros blocos (Fases, Atividades, etc.) também ficam aqui dentro --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
                {{-- (Aqui entrariam suas seções de Fases, Atividades, Tarefas, etc. que você já tem) --}}
            </div>

            {{-- Instruções e título --}}
            <div class="space-y-3 mt-14" style="margin-top: 20px;">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-light-bulb class="w-6 h-6 text-primary-500" />
                    Gerar Recomendação
                </h1>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Abaixo está o contexto do projeto. Esse conteúdo será enviado para a API de recomendação baseada em IA.<br>
                    <span class="text-primary-700 font-medium" style="margin-bottom: 10px; display:inline-block">Verifique se todas as informações estão corretas antes de continuar.</span>
                </p>
            </div>

            {{-- Bloco do contexto JSON --}}
            <div class="bg-gray-50 p-5 rounded-lg overflow-auto max-h-[400px] text-sm font-mono whitespace-pre-wrap border border-gray-200 text-gray-700 shadow-inner">
                {{ $contexto }}
            </div>

            {{-- Botão --}}
            <div class="flex justify-end mt-4" style="margin-top: 20px;">
                <x-filament::button
                    wire:click="gerarRecomendacao"
                    color="primary"
                    size="xl"
                    wire:loading.attr="disabled"
                    wire:target="gerarRecomendacao"
                >
                    <span wire:loading.remove wire:target="gerarRecomendacao">Gerar Recomendação</span>
                    <span wire:loading wire:target="gerarRecomendacao">Gerando...</span>
                </x-filament::button>
            </div>
        @endif


        {{-- ===================================================================== --}}
        {{-- ESTA PARTE SÓ APARECE DEPOIS QUE A RESPOSTA DA IA CHEGAR --}}
        {{-- ===================================================================== --}}
        @if ($resposta)
            <div class="mt-10 space-y-2">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-primary-500" />
                    <h2 class="text-lg font-semibold text-primary-800">Resposta da Recomendação:</h2>
                </div>
                <div class="bg-primary-50 p-5 border border-primary-100 rounded-lg shadow-sm text-sm text-gray-800 whitespace-pre-wrap">
                    {{-- Usa a propriedade pública $resposta, e não a sessão --}}
                    {!! nl2br(e($resposta)) !!}
                </div>

                {{-- Renderiza Mermaid --}}
                <div class="mt-6">
                    <div class="mermaid">
                        {!! $resposta !!}
                    </div>
                </div>

                @push('scripts')
                    <script type="module">
                        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
                        mermaid.initialize({ startOnLoad: true });
                    </script>
                @endpush
            </div>
        @endif

    </div>
</x-filament::page>