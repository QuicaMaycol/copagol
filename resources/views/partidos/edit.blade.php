<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Editar Partido') }}
            </h2>
            <a href="{{ route('campeonatos.show', $partido->campeonato) }}" class="flex items-center justify-center bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-3 rounded-lg transition-colors duration-300 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span class="hidden sm:inline ml-2">Volver</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $localTeamId = old('equipo_local_id', $partido->equipo_local_id);
                $visitorTeamId = old('equipo_visitante_id', $partido->equipo_visitante_id);
            @endphp
            <form action="{{ route('partidos.update', $partido) }}" method="POST" x-data="{ activeTab: 'local' }">
                @csrf
                @method('PUT')

                <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                    <!-- Match Header -->
                    <div class="p-6 border-b border-gray-700">
                        <div class="flex flex-col items-center">
                            <!-- Teams Row -->
                            <div class="w-full flex justify-between items-start">
                                <div class="text-center w-2/5">
                                    <img src="{{ $partido->equipoLocal->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoLocal->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo de {{ $partido->equipoLocal->nombre }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover mx-auto mb-3 border-2 border-gray-600">
                                    <h2 class="text-base sm:text-xl font-bold text-gray-100 break-words">{{ $partido->equipoLocal->nombre }}</h2>
                                </div>

                                <div class="text-center flex-shrink-0 pt-8 px-2">
                                    <span class="text-gray-500 font-bold text-lg">VS</span>
                                </div>

                                <div class="text-center w-2/5">
                                    <img src="{{ $partido->equipoVisitante->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoVisitante->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo de {{ $partido->equipoVisitante->nombre }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover mx-auto mb-3 border-2 border-gray-600">
                                    <h2 class="text-base sm:text-xl font-bold text-gray-100 break-words">{{ $partido->equipoVisitante->nombre }}</h2>
                                </div>
                            </div>

                            <!-- Score and Status Row -->
                            <div class="w-full mt-6" x-data="{ goles_local: {{ old('goles_local', $partido->goles_local) ?? 0 }}, goles_visitante: {{ old('goles_visitante', $partido->goles_visitante) ?? 0 }} }">
                                <div class="flex justify-center items-center space-x-2">
                                    <!-- Local Score -->
                                    <div class="flex items-center bg-gray-700 rounded-lg p-1">
                                        <button type="button" @click="goles_local = Math.max(0, goles_local - 1)" class="text-xl h-10 w-10 text-gray-400 hover:text-white transition-colors duration-200">-</button>
                                        <input type="number" name="goles_local" x-model="goles_local" class="w-16 text-center text-4xl font-bold border-0 bg-transparent text-white p-0 focus:ring-0">
                                        <button type="button" @click="goles_local++" class="text-xl h-10 w-10 text-gray-400 hover:text-white transition-colors duration-200">+</button>
                                    </div>

                                    <span class="text-3xl font-bold mx-2 text-gray-500">-</span>

                                    <!-- Visitor Score -->
                                    <div class="flex items-center bg-gray-700 rounded-lg p-1">
                                        <button type="button" @click="goles_visitante = Math.max(0, goles_visitante - 1)" class="text-xl h-10 w-10 text-gray-400 hover:text-white transition-colors duration-200">-</button>
                                        <input type="number" name="goles_visitante" x-model="goles_visitante" class="w-16 text-center text-4xl font-bold border-0 bg-transparent text-white p-0 focus:ring-0">
                                        <button type="button" @click="goles_visitante++" class="text-xl h-10 w-10 text-gray-400 hover:text-white transition-colors duration-200">+</button>
                                    </div>
                                </div>
                                <div class="mt-4 max-w-xs mx-auto">
                                    <label for="estado" class="sr-only">Estado del partido</label>
                                    <select name="estado" id="estado" class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="pendiente" @selected($partido->estado == 'pendiente')>Pendiente</option>
                                        <option value="en_juego" @selected($partido->estado == 'en_juego')>En Juego</option>
                                        <option value="finalizado" @selected($partido->estado == 'finalizado')>Finalizado</option>
                                        <option value="suspendido" @selected($partido->estado == 'suspendido')>Suspendido</option>
                                        <option value="reprogramado" @selected($partido->estado == 'reprogramado')>Reprogramado</option>
                                        <option value="cancelado" @selected($partido->estado == 'cancelado')>Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Player Stats Tabs -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-200 mb-4">Estadísticas de Jugadores</h3>
                        <div class="border-b border-gray-700 mb-4">
                            <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
                                <button type="button" @click="activeTab = 'local'" :class="{ 'border-indigo-500 text-indigo-400': activeTab === 'local', 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-400': activeTab !== 'local' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-base">
                                    {{ $partido->equipoLocal->nombre }}
                                </button>
                                <button type="button" @click="activeTab = 'visitante'" :class="{ 'border-indigo-500 text-indigo-400': activeTab === 'visitante', 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-400': activeTab !== 'visitante' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-base">
                                    {{ $partido->equipoVisitante->nombre }}
                                </button>
                            </nav>
                        </div>

                        <div x-show="activeTab === 'local'" x-transition>
                            @include('partidos.partials.player-stats-table', ['equipo' => $partido->equipoLocal, 'playerStats' => $playerStats])
                        </div>
                        <div x-show="activeTab === 'visitante'" x-transition style="display: none;">
                            @include('partidos.partials.player-stats-table', ['equipo' => $partido->equipoVisitante, 'playerStats' => $playerStats])
                        </div>
                    </div>
                </div>

                <!-- Other Details (Collapsible) -->
                <div class="bg-gray-800 shadow-xl rounded-lg mt-8" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-full p-6 text-left font-semibold text-gray-200 flex justify-between items-center">
                        <span>Más Detalles del Partido</span>
                        <svg class="w-5 h-5 transform transition-transform text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-6 border-t border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="fecha_partido" class="block text-sm font-medium text-gray-300">Fecha y Hora</label>
                                <input type="datetime-local" name="fecha_partido" id="fecha_partido" value="{{ old('fecha_partido', \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="ubicacion_partido" class="block text-sm font-medium text-gray-300">Ubicación</label>
                                <input type="text" name="ubicacion_partido" id="ubicacion_partido" value="{{ old('ubicacion_partido', $partido->ubicacion_partido) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="equipo_local_id_detail" class="block text-sm font-medium text-gray-300">Equipo Local</label>
                                <select name="equipo_local_id" id="equipo_local_id_detail" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" @selected($localTeamId == $team->id)>{{ $team->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="equipo_visitante_id_detail" class="block text-sm font-medium text-gray-300">Equipo Visitante</label>
                                <select name="equipo_visitante_id" id="equipo_visitante_id_detail" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" @selected($visitorTeamId == $team->id)>{{ $team->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col-reverse gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('campeonatos.show', $partido->campeonato) }}" class="w-full sm:w-auto text-center text-gray-300 bg-gray-700 hover:bg-gray-600 font-semibold py-3 px-6 rounded-lg transition-colors duration-200">Cancelar</a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold shadow-lg">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        input[type="datetime-local"]::-webkit-calendar-picker-indicator { filter: invert(1); }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campeonatoId = {{ $partido->campeonato_id }};
            const equipoLocalSelect = document.getElementById('equipo_local_id_detail');
            const equipoVisitanteSelect = document.getElementById('equipo_visitante_id_detail');

            function updateOpponentList() {
                const equipoLocalId = equipoLocalSelect.value;

                if (!equipoLocalId) {
                    return;
                }

                const currentVisitorId = equipoVisitanteSelect.value;

                equipoVisitanteSelect.innerHTML = '<option value="">Cargando...</option>';
                equipoVisitanteSelect.disabled = true;

                const url = `/campeonatos/${campeonatoId}/equipos/${equipoLocalId}/oponentes`;

                fetch(url)
                    .then(response => response.json())
                    .then(oponentes => {
                        equipoVisitanteSelect.innerHTML = '<option value="">Seleccione un equipo</option>';
                        
                        oponentes.forEach(oponente => {
                            const option = document.createElement('option');
                            option.value = oponente.id;
                            option.textContent = `${oponente.nombre} (${oponente.jugado ? 'Jugado' : 'Pendiente'})`;
                            option.disabled = oponente.jugado;

                            // Si el oponente es el equipo que ya estaba seleccionado, lo marcamos
                            if (oponente.id == currentVisitorId) {
                                option.selected = true;
                            }
                            
                            if (oponente.jugado) {
                                option.style.color = '#f87171'; // Red-400
                            } else {
                                option.style.color = '#4ade80'; // Green-400
                            }
                            equipoVisitanteSelect.appendChild(option);
                        });

                        equipoVisitanteSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error al cargar los oponentes:', error);
                        equipoVisitanteSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            }

            // Add event listener
            equipoLocalSelect.addEventListener('change', updateOpponentList);

            // Initial load
            if (equipoLocalSelect.value) {
                updateOpponentList();
            }
        });
    </script>
</x-app-layout>