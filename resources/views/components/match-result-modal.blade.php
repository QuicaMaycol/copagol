@props(['campeonato'])

<div
    x-data="matchResultModal()"
    x-show="show"
    x-on:open-match-result-modal.window="open($event.detail.match)"
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
    style="display: none;"
>
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl mx-4" @click.away="close()">
        <h2 class="text-2xl font-bold text-copa-blue-900 mb-4">Cargar Resultado del Partido</h2>

        <template x-if="match">
            <form :action="'/partidos/' + match.id + '/store-result'" method="POST">
                @csrf
                <div class="grid grid-cols-3 items-center text-center mb-6">
                    <div class="font-semibold text-lg" x-text="match.equipo_local.nombre"></div>
                    <div class="flex items-center justify-center space-x-4">
                        <input type="number" name="goles_local" x-model="golesLocalInput" class="w-20 text-center text-2xl font-bold border-gray-300 rounded-md" min="0">
                        <span class="text-2xl">-</span>
                        <input type="number" name="goles_visitante" x-model="golesVisitanteInput" class="w-20 text-center text-2xl font-bold border-gray-300 rounded-md" min="0">
                    </div>
                    <div class="font-semibold text-lg" x-text="match.equipo_visitante.nombre"></div>
                </div>

                <h3 class="text-xl font-semibold text-copa-blue-900 mt-6 mb-4">Estadísticas de Jugadores</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Local Team Players -->
                    <div>
                        <h4 class="font-bold mb-2" x-text="match.equipo_local.nombre"></h4>
                        <div class="space-y-2">
                            <template x-for="jugador in match.equipo_local.jugadores" :key="jugador.id">
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="font-semibold" x-text="jugador.nombre"></p>
                                    <div class="grid grid-cols-4 gap-2 mt-2">
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_goles'" class="text-sm text-gray-600">Goles</label>
                                            <input :name="'jugadores[' + jugador.id + '][goles]'" :id="'jugador_' + jugador.id + '_goles'" type="number" min="0" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'goles')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_asistencias'" class="text-sm text-gray-600">Asist.</label>
                                            <input :name="'jugadores[' + jugador.id + '][asistencias]'" :id="'jugador_' + jugador.id + '_asistencias'" type="number" min="0" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'asistencias')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_amarillas'" class="text-sm text-gray-600">T.A.</label>
                                            <input :name="'jugadores[' + jugador.id + '][amarillas]'" :id="'jugador_' + jugador.id + '_amarillas'" type="number" min="0" max="1" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'tarjetas_amarillas')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_rojas'" class="text-sm text-gray-600">T.R.</label>
                                            <input :name="'jugadores[' + jugador.id + '][rojas]'" :id="'jugador_' + jugador.id + '_rojas'" type="number" min="0" max="1" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'tarjetas_rojas')">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Visitor Team Players -->
                    <div>
                        <h4 class="font-bold mb-2" x-text="match.equipo_visitante.nombre"></h4>
                        <div class="space-y-2">
                            <template x-for="jugador in match.equipo_visitante.jugadores" :key="jugador.id">
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="font-semibold" x-text="jugador.nombre"></p>
                                    <div class="grid grid-cols-4 gap-2 mt-2">
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_goles'" class="text-sm text-gray-600">Goles</label>
                                            <input :name="'jugadores[' + jugador.id + '][goles]'" :id="'jugador_' + jugador.id + '_goles'" type="number" min="0" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'goles')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_asistencias'" class="text-sm text-gray-600">Asist.</label>
                                            <input :name="'jugadores[' + jugador.id + '][asistencias]'" :id="'jugador_' + jugador.id + '_asistencias'" type="number" min="0" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'asistencias')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_amarillas'" class="text-sm text-gray-600">T.A.</label>
                                            <input :name="'jugadores[' + jugador.id + '][amarillas]'" :id="'jugador_' + jugador.id + '_amarillas'" type="number" min="0" max="1" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'tarjetas_amarillas')">
                                        </div>
                                        <div>
                                            <label :for="'jugador_' + jugador.id + '_rojas'" class="text-sm text-gray-600">T.R.</label>
                                            <input :name="'jugadores[' + jugador.id + '][rojas]'" :id="'jugador_' + jugador.id + '_rojas'" type="number" min="0" max="1" class="w-full border-gray-300 rounded-md text-sm" :value="getPlayerStat(jugador.id, 'tarjetas_rojas')">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <button type="button" @click="close()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium">Guardar Resultado</button>
                </div>
            </form>
        </template>
    </div>
</div>

@push('scripts')
<script>
    function matchResultModal() {
        return {
            show: false,
            match: null,
            playerStats: {}, // To store player-specific stats
            golesLocalInput: 0,
            golesVisitanteInput: 0,

            open(match) {
                this.match = match;
                this.show = true;
                this.playerStats = {}; // Reset player stats

                // Pre-fill match goals
                this.golesLocalInput = match.goles_local ?? 0;
                this.golesVisitanteInput = match.goles_visitante ?? 0;

                // Fetch player statistics
                fetch(`/partidos/${match.id}/estadisticas`)
                    .then(response => response.json())
                    .then(data => {
                        this.playerStats = data;
                    })
                    .catch(error => {
                        console.error('Error fetching player statistics:', error);
                        // Optionally, show an error message to the user
                    });
            },
            close() {
                this.show = false;
                this.match = null;
                this.playerStats = {};
                this.golesLocalInput = 0;
                this.golesVisitanteInput = 0;
            },
            // Helper to get player stat for a specific type
            getPlayerStat(jugadorId, statType) {
                return this.playerStats[jugadorId]?.[statType] ?? 0;
            }
        }
    }
</script>
@endpush