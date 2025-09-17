@props(['sancionados', 'conAmarilla', 'loading'])

<div
    x-show="showSancionadosModal"
    x-on:keydown.escape.window="showSancionadosModal = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showSancionadosModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
             @click="showSancionadosModal = false"
             aria-hidden="true"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showSancionadosModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">

            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                Jugadores Sancionados
            </h3>

            <div class="mt-4">
                <div x-show="loading" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">Cargando...</p>
                </div>

                <div x-show="!loading">
                    <!-- Suspendidos -->
                    <h4 class="text-lg font-semibold text-red-600 dark:text-red-500 mt-4 mb-2">Suspendidos para este partido</h4>
                    <div x-show="sancionados.length > 0">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="jugador in sancionados" :key="jugador.nombre">
                                <li class="py-2 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-200" x-text="jugador.nombre"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="jugador.equipo"></p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" x-text="jugador.tipo_sancion"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div x-show="sancionados.length === 0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay jugadores suspendidos para este partido.</p>
                    </div>

                    <!-- Con Amarilla -->
                    <h4 class="text-lg font-semibold text-yellow-600 dark:text-yellow-500 mt-6 mb-2">Apercibidos (con Tarjeta Amarilla)</h4>
                    <div x-show="conAmarilla.length > 0">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="jugador in conAmarilla" :key="jugador.nombre">
                                <li class="py-2 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-200" x-text="jugador.nombre"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="jugador.equipo"></p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" x-text="jugador.cantidad + ' Amarilla(s)'"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                     <div x-show="conAmarilla.length === 0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay jugadores con tarjetas amarillas en los equipos de este partido.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button @click="showSancionadosModal = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>