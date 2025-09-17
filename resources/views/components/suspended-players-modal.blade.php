<div
    x-data="{
        show: false,
        players: [],
        campeonatoId: null,
        faseId: null,
        fetchSuspendedPlayers() {
            fetch(`/campeonatos/${this.campeonatoId}/fases/${this.faseId}/suspended-players`)
                .then(response => response.json())
                .then(data => {
                    this.players = data;
                })
                .catch(error => {
                    console.error('Error fetching suspended players:', error);
                    this.players = [];
                });
        }
    }"
    x-show="show"
    @open-suspended-players-modal.window="
        show = true;
        campeonatoId = $event.detail.campeonatoId;
        faseId = $event.detail.faseId;
        fetchSuspendedPlayers();
    "
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            Jugadores Sancionados
                        </h3>
                        <div class="mt-2">
                            <template x-if="players.length > 0">
                                <ul class="divide-y divide-gray-200">
                                    <template x-for="player in players" :key="player.id">
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" x-text="`${player.nombre} ${player.apellido}`"></p>
                                                <p class="text-sm text-gray-500" x-text="`Equipo: ${player.equipo.nombre}`"></p>
                                                <p class="text-sm text-gray-500" x-text="`Partidos de suspensión restantes: ${player.suspension_matches}`"></p>
                                            </div>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Suspendido
                                            </span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="players.length === 0">
                                <p class="text-sm text-gray-500">No hay jugadores sancionados para esta fase.</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="show = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
