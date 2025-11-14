<div>
    @if ($this->codigoMermaid || $this->justificacao)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-sparkles class="w-5 h-5 text-primary-500" />
                    Última Recomendação
                </div>
            </x-slot>

          <div class="space-y-8"
              x-data="{
                    codigo: @js($this->codigoMermaid),
                    copiado: false,
                    async copiarCodigo() {
                        if (!this.codigo) return;
                        try { await navigator.clipboard.writeText(this.codigo); this.copiado = true; } catch (e) {}
                        setTimeout(() => this.copiado = false, 2000);
                    }
                 }"
              x-init="$nextTick(() => window.deferRenderMermaidView(codigo))"
              x-effect="window.deferRenderMermaidView(codigo)"
            >
                {{-- Diagrama --}}
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <p class="text-sm text-gray-600 lg:max-w-xl">
                            Utilize o diagrama abaixo para visualizar a sequência recomendada e copie o código Mermaid para ajustar ou compartilhar.
                        </p>
                        <div class="flex items-center gap-2">
                            <span x-show="copiado" x-transition class="text-xs font-semibold text-success-500">Copiado!</span>
                            <x-filament::button type="button" color="primary" size="sm"
                                x-on:click="copiarCodigo" x-bind:disabled="!codigo" icon="heroicon-o-clipboard-document"
                                icon-position="before" class="shadow-sm">Copiar Mermaid</x-filament::button>
                        </div>
                    </div>

                    <div class="bg-gray-900 rounded-lg w-full overflow-hidden border border-gray-700 flex-1 shadow-md" style="height: 540px;">
                        <div id="mermaid-diagram-view" class="w-full h-full">
                            <p class="text-gray-400 p-4 text-sm">O diagrama aparecerá aqui...</p>
                        </div>
                    </div>
                </div>

                {{-- Justificação --}}
                @if ($this->justificacao)
                    <div class="rounded-xl border border-primary-700/70 bg-primary-600 p-8 lg:p-9 text-white shadow-lg space-y-5">
                        <div class="text-lg font-semibold flex items-center gap-2 tracking-wide">
                            <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                            Justificação
                        </div>
                        <div class="prose prose-sm max-w-none prose-invert text-white
                            [&_*]:text-white [&_strong]:text-white [&_b]:text-white [&_p]:text-white [&_li]:text-white
                            [&_span]:text-white [&_a]:text-white [&_code]:text-white [&_pre]:text-white [&_thead_th]:text-white
                            [&_tbody_td]:text-white [&_li::marker]:text-white [&_h1]:text-white [&_h2]:text-white [&_h3]:text-white
                            [&_h4]:text-white [&_h5]:text-white [&_h6]:text-white [&_em]:text-white">
                            {!! \Illuminate\Support\Str::markdown($this->justificacao) !!}
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        @push('scripts')
        <script>
            // Função de renderização adiada: garante que mesmo que Alpine rode antes do módulo mermaid carregar, o diagrama apareça.
            window.deferRenderMermaidView = function(code){
                const attempt = () => {
                    if (typeof window.renderMermaidView === 'function') {
                        window.renderMermaidView(code);
                    } else {
                        setTimeout(attempt, 150);
                    }
                };
                attempt();
            };
        </script>
        <script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>
        <script type="module">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';
            mermaid.initialize({ startOnLoad: false, theme: 'dark' });

            window.renderMermaidView = async function(content) {
                const el = document.getElementById('mermaid-diagram-view');
                if (!el || !content) { if (el) el.innerHTML = '<p class="text-gray-400 p-4">O diagrama aparecerá aqui...</p>'; return; }

                if (window.panZoomInstanceView) { window.panZoomInstanceView.destroy(); window.panZoomInstanceView = null; }

                const graphId = 'gv_' + Date.now();
                try {
                    const { svg } = await mermaid.render(graphId, content);
                    el.innerHTML = svg;
                    const svgElement = el.querySelector('svg');
                    if (svgElement && window.svgPanZoom) {
                        svgElement.removeAttribute('width'); svgElement.removeAttribute('height');
                        svgElement.style.width = '100%'; svgElement.style.height = '1200px';
                        setTimeout(() => {
                            window.panZoomInstanceView = svgPanZoom(svgElement, { zoomEnabled: true, controlIconsEnabled: true, fit: true, center: true, minZoom: 0.1, maxZoom: 20 });
                        }, 300);
                    }
                } catch (e) {
                    el.innerHTML = '<p class="text-red-400 p-4">Erro ao renderizar o diagrama: ' + e.message + '</p>';
                    console.error(e);
                }
            }
            // Auto-render na carga do script caso já haja código.
            // Removido auto-render direto; x-init já chama deferRenderMermaidView que aguardará mermaid carregar.
        </script>
        @endpush
    @endif
</div>
