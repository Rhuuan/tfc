<x-filament::page>
    <div class="space-y-10 max-w-6xl mx-auto px-6 md:px-8">

        {{-- Bloco de informações do projeto (padronizado) --}}
        <x-filament::section style="margin-bottom: 20px;">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-briefcase class="w-6 h-6 text-primary-600" />
                    <span class="text-lg font-semibold text-primary-800">Informações do Projeto</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-800 leading-relaxed">
                <div>
                    <span class="font-semibold">Projeto:</span> {{ $record['nome'] ?? '-' }}
                </div>

                <div>
                    <span class="font-semibold">Criado em:</span>
                    {{ $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('d/m/Y H:i') : '-' }}
                </div>
                <div class="md:col-span-2">
                    <span class="font-semibold">Descrição:</span> {{ $record['descricao'] ?? '-' }}
                </div>
            </div>
        </x-filament::section>

        {{-- Blocos informativos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">

            {{-- Fases --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-primary-500" />
                        <span class="text-lg font-semibold text-primary-700">Fases vinculadas</span>
                    </div>
                </x-slot>

                @if (!empty($record['fases']))
                    <ul class="divide-y divide-gray-200 text-sm text-gray-800">
                        @foreach ($record['fases'] as $fase)
                            <li class="py-2 rounded-md transition">
                                <span class="font-semibold">{{ $fase['nome'] ?? '-' }}</span>
                                <div class="text-xs text-gray-500">{{ $fase['descricao'] ?? '' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-xs text-gray-500">Nenhuma fase vinculada.</span>
                @endif
            </x-filament::section>

            {{-- Atividades --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clipboard-document class="w-5 h-5 text-primary-500" />
                        <span class="text-lg font-semibold text-primary-700">Atividades vinculadas</span>
                    </div>
                </x-slot>

                @if (!empty($record['atividades']))
                    <ul class="divide-y divide-gray-200 text-sm text-gray-800">
                        @foreach ($record['atividades'] as $atividade)
                            <li class="py-2 rounded-md transition">
                                <span class="font-semibold">{{ $atividade['nome'] ?? '-' }}</span>
                                <div class="text-xs text-gray-500">{{ $atividade['descricao'] ?? '' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-xs text-gray-500">Nenhuma atividade vinculada.</span>
                @endif
            </x-filament::section>

            {{-- Tarefas --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-check-badge class="w-5 h-5 text-primary-500" />
                        <span class="text-lg font-semibold text-primary-700">Tarefas vinculadas</span>
                    </div>
                </x-slot>

                @if (!empty($record['tarefas']))
                    <ul class="divide-y divide-gray-200 text-sm text-gray-800">
                        @foreach ($record['tarefas'] as $tarefa)
                            <li class="py-2 rounded-md transition">
                                <span class="font-semibold">{{ $tarefa['nome'] ?? '-' }}</span>
                                <div class="text-xs text-gray-500">{{ $tarefa['descricao'] ?? '' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-xs text-gray-500">Nenhuma tarefa vinculada.</span>
                @endif
            </x-filament::section>

            {{-- Métodos/Ferramentas --}}
            <x-filament::section >
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-primary-500" />
                        <span class="text-lg font-semibold text-primary-700">Métodos/Ferramentas vinculados</span>
                    </div>
                </x-slot>

                @php
                    $metodoFerramentas = $record['metodo_ferramentas'] ?? [];
                @endphp

                @if (!empty($metodoFerramentas))
                    <ul class="divide-y divide-gray-200 text-sm text-gray-800">
                        @foreach ($metodoFerramentas as $mf)
                            <li class="py-2 rounded-md transition">
                                <span class="font-semibold">{{ $mf['nome'] ?? '-' }}</span>
                                <div class="text-xs text-gray-500">{{ $mf['descricao'] ?? '' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-xs text-gray-500">Nenhum método/ferramenta vinculado.</span>
                @endif
            </x-filament::section>
        </div>

        {{-- Instruções e título --}}
        <div class="space-y-3 mt-14" style="margin-top: 20px;">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-sparkles class="w-6 h-6 text-primary-500" />
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
            >
                Gerar Recomendação
            </x-filament::button>
        </div>

        {{-- Resposta da Recomendação --}}
        @if (session()->has('resposta'))
            <div class="mt-10 space-y-2">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-primary-500" />
                    <h2 class="text-lg font-semibold text-primary-800">Resposta da Recomendação:</h2>
                </div>
                <div class="bg-primary-50 p-5 border border-primary-100 rounded-lg shadow-sm text-sm text-gray-800 whitespace-pre-wrap">
                    {{ session('resposta') }}
                </div>
            </div>
        @endif

    </div>
</x-filament::page>
