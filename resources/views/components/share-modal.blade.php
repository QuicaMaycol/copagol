@props(['shareUrl' => ''])

<div
    x-data="{
        show: false,
        url: '{{ $shareUrl }}',
        copyText: 'Copiar',
        init() {
            $watch('show', value => {
                if (value) {
                    this.copyText = 'Copiar'; // Reset button text when modal opens
                }
            });
        }
    }"
    x-init="init()"
    x-on:open-share-modal.window="show = true; url = $event.detail.url;"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="share-modal-title"
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
             class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
             @click.away="show = false">

            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="share-modal-title">
                    Compartir Campeonato
                </h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Cualquier persona con este enlace podrá ver la información pública del campeonato.</p>
                <div class="relative">
                    <input type="text" :value="url" readonly class="w-full px-3 py-2 text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none">
                    <button @click="navigator.clipboard.writeText(url); copyText = '¡Copiado!'; setTimeout(() => copyText = 'Copiar', 2000)" 
                            class="absolute inset-y-0 right-0 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                        <span x-text="copyText"></span>
                    </button>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button @click="show = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 dark:bg-gray-600 dark:text-gray-200 border border-transparent rounded-md shadow-sm hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
