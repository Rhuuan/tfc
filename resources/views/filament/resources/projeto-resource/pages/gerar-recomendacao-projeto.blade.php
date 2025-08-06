<x-filament::page>
    <div class="space-y-6">
        <!-- Caixa principal -->
        <div class="p-6 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 shadow-sm text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-extrabold text-indigo-800 dark:text-indigo-300 mb-4">
                📘 Instruções para Geração de Recomendação
            </h2>

            <div class="space-y-6 text-base leading-relaxed">
                <!-- Sistema -->
                <section>
                    <h3 class="font-semibold text-indigo-700 dark:text-indigo-200 text-lg">🔧 Sistema</h3>
                    <ul class="list-disc list-inside ml-4 mt-2 text-gray-800 dark:text-gray-200">
                        <li>Você é um especialista em UX e elicitação de requisitos. Sua tarefa é analisar os dados de um projeto e recomendar um fluxo de trabalho otimizado.</li>
                    </ul>
                </section>

                <!-- Instrução -->
                <section>
                    <h3 class="font-semibold text-indigo-700 dark:text-indigo-200 text-lg">🧭 Instrução</h3>
                    <ul class="list-disc list-inside ml-4 mt-2 text-gray-800 dark:text-gray-200 space-y-1">
                        <li>Analise as relações</li>
                        <li>Identifique o fluxo de trabalho mais eficiente</li>
                        <li>Organize o fluxo em uma sequência lógica de etapas</li>
                        <li>Para cada etapa, indique uma recomendação</li>
                    </ul>
                </section>

                <!-- Contexto -->
                <section>
                    <h3 class="font-semibold text-indigo-700 dark:text-indigo-200 text-lg">📌 Contexto</h3>
                    <ul class="list-disc list-inside ml-4 mt-2 text-gray-800 dark:text-gray-200 space-y-1">
                        <li>Nome: Sistema de Gerenciamento de Requisitos</li>
                        <li>Descrição: Projeto destinado a estruturar e documentar todas as etapas do processo de elicitação de requisitos, 
                            incluindo o uso de métodos, ferramentas, tarefas, fases e atividades, visando maior organização e rastreabilidade
                             no desenvolvimento de sistemas.</li>
                    </ul>
                </section>

                <!-- Formato da resposta -->
                <section>
                    <h3 class="font-semibold text-indigo-700 dark:text-indigo-200 text-lg">📝 Formato da resposta</h3>
                    <ul class="list-disc list-inside ml-4 mt-2 text-gray-800 dark:text-gray-200 space-y-1">
                        <li>Diagrama Mermaid: código ou imagem para visualizar o fluxo</li>
                        <li>Justificativa: explicação para cada recomendação</li>
                    </ul>
                </section>
            </div>
        </div>

        <!-- Botão -->
        <x-filament::button
            color="primary"
            size="lg"
            class="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold"
        >
            🚀 Enviar e Gerar Recomendação
        </x-filament::button>
    </div>
</x-filament::page>
