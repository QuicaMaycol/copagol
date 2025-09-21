@props(['campeonato'])

<div
    x-data="{ show: false }"
    x-on:open-reglamento-modal.window="show = true"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
             @click="show = false"
             aria-hidden="true"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
             @click.away="show = false">

            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                    Reglamento del Torneo
                </h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mt-4 prose max-w-none dark:prose-invert">
                @if ($campeonato->reglamento_tipo === 'pdf' && $campeonato->reglamento_path)
                    <iframe src="{{ Storage::url($campeonato->reglamento_path) }}" class="w-full h-[70vh] rounded border border-gray-200 dark:border-gray-700"></iframe>
                @elseif ($campeonato->reglamento_tipo === 'texto' && $campeonato->reglamento_texto)
                    <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md whitespace-pre-wrap font-sans text-gray-800 dark:text-gray-200">
                        {{ $campeonato->reglamento_texto }}
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No hay un reglamento disponible para este campeonato.</p>
                @endif
            </div>

            <div class="mt-6 text-right">
                <button @click="show = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
