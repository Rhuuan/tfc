<x-filament::page>
    <div class="space-y-6">
        {{-- Mensagem de boas-vindas --}}
        <h2 class="text-2xl font-bold">
            Bem-vindo, {{ $this->getUserName() }} 👋
        </h2>

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
