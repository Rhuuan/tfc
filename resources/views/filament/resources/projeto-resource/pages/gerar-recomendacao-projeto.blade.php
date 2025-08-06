<x-filament::page>
    <div class="space-y-6">
        <div class="p-6 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 shadow-sm text-gray-900 dark:text-gray-100">
            <h2 class="text-2xl font-extrabold text-indigo-800 dark:text-indigo-300 mb-4">
                📘 Instruções para Geração de Recomendação
            </h2>

            <div class="space-y-4 text-base leading-relaxed">
                <p>
                    <span class="font-semibold text-indigo-700 dark:text-indigo-200">🔧 Sistema:</span>
                    Você é um especialista em UX e elicitação de requisitos. Sua tarefa é analisar os dados de um projeto e recomendar um fluxo de trabalho otimizado.
                </p>

                <!-- Instrução (ATUALIZADO) -->
                <div>
                    <p class="font-semibold text-indigo-700 dark:text-indigo-200">🧭 Instrução:</p>
                        <li>Analise as relações</li>
                        <li>Identifique o fluxo de trabalho mais eficiente</li>
                        <li>Organize o fluxo em uma sequência lógica de etapas</li>
                        <li>Para cada etapa, indique uma recomendação</li>
                    </ol>
                </div>

                <!-- Contexto (ATUALIZADO) -->
                <div>
                    <p class="font-semibold text-indigo-700 dark:text-indigo-200">📌 Contexto:</p>
                        <li><strong class="text-indigo-700 dark:text-indigo-200">Nome:</strong> Sistema de Gerenciamento de Requisitos</li>
                        <li><strong class="text-indigo-700 dark:text-indigo-200">Descrição:</strong> Projeto destinado a estruturar e documentar todas as etapas do processo de elicitação de requisitos, incluindo o uso de métodos, ferramentas, tarefas, fases e atividades, visando maior organização e rastreabilidade no desenvolvimento de sistemas.</li>
                    </ul>
                </div>


                <!-- Formato da resposta (já estava certo) -->
                <div>
                    <p class="font-semibold text-indigo-700 dark:text-indigo-200">📝 Formato da resposta:</p>
                        <li><strong>Diagrama Mermaid:</strong> código ou imagem para visualizar o fluxo</li>
                        <li><strong>Justificativa:</strong> explicação para cada recomendação</li>
                    </ul>
                </div>
            </div>
        </div>

        <x-filament::button
            color="primary"
            size="lg"
            class="w-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold"
        >
            🚀 Enviar e Gerar Recomendação
        </x-filament::button>
    </div>
</x-filament::page>
