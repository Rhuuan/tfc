<x-filament::page>
    <div class="space-y-6">
        {{-- Mensagem de boas-vindas --}}
        <h2 class="text-2xl font-bold">
            Bem-vindo, {{ $this->getUserName() }} 👋
        </h2>

        {{-- Introdução ao fluxo do sistema --}}
        
            <div class="space-y-3 text-sm text-gray-700">
                <p class="font-semibold text-primary-700">
                    Use o menu lateral para gerenciar:
                </p>
                <ul class="space-y-2 list-disc list-inside">
                    <li><strong>Projetos:</strong> O escopo geral do seu trabalho.</li>
                    <li><strong>Fases:</strong> Etapas macro do projeto (com prazo).</li>
                    <li><strong>Atividades:</strong> Ações dentro de cada Fase.</li>
                    <li><strong>Tarefas:</strong> A menor unidade de trabalho, ação pontual.</li>
                    <li><strong>Métodos e Ferramentas:</strong> Recursos de apoio para vincular às suas ações.</li>
                </ul>
                <p class="text-gray-800 font-medium">
                    <strong>Sugestão Rápida:</strong> Comece criando um Projeto e use a função <em>Gerar Recomendação</em> para que o sistema sugira automaticamente Fases, Atividades e Métodos para você.
                </p>
            </div>

        {{-- Grid de cards com estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <p class="text-sm text-gray-500">Projetos</p>
                <p class="text-xl font-bold text-primary-600">
                    {{ \App\Models\Projeto::where('user_id', auth()->id())->count() }}
                </p>
            </x-filament::card>

            <x-filament::card>
                <p class="text-sm text-gray-500">Fases</p>
                <p class="text-xl font-bold text-primary-600">
                    {{ \App\Models\Fase::where('user_id', auth()->id())->count() }}
                </p>
            </x-filament::card>

            <x-filament::card>
                <p class="text-sm text-gray-500">Atividades</p>
                <p class="text-xl font-bold text-primary-600">
                    {{ \App\Models\Atividade::where('user_id', auth()->id())->count() }}
                </p>
            </x-filament::card>

            <x-filament::card>
                <p class="text-sm text-gray-500">Tarefas</p>
                <p class="text-xl font-bold text-primary-600">
                    {{ \App\Models\Tarefa::where('user_id', auth()->id())->count() }}
                </p>
            </x-filament::card>

            <x-filament::card>
                <p class="text-sm text-gray-500">Métodos e Ferramentas</p>
                <p class="text-xl font-bold text-primary-600">
                    {{ \App\Models\MetodoFerramenta::where('user_id', auth()->id())->count() }}
                </p>
            </x-filament::card>
        </div>
    </div>
</x-filament::page>
