<x-app-layout>
    @push('styles')
    <style>
        .score-input-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .score-input {
            width: 100px;
            text-align: center;
            font-size: 3rem;
            font-weight: bold;
            border: none;
            background-color: transparent;
            color: inherit;
        }
        .score-btn {
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            padding: 0 1rem;
            user-select: none;
            color: #4a5568;
        }
        .dark .score-btn {
            color: #cbd5e0;
        }
    </style>
    @endpush

    <div class="py-4 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @php
                $localTeamId = old('equipo_local_id', $partido->equipo_local_id);
                $visitorTeamId = old('equipo_visitante_id', $partido->equipo_visitante_id);
            @endphp
            <form action="{{ route('partidos.update', $partido) }}" method="POST" x-data="{ activeTab: 'local' }">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <!-- Match Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div class="text-center w-1/3">
                                <img src="{{ $partido->equipoLocal->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoLocal->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo de {{ $partido->equipoLocal->nombre }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover mx-auto mb-2">
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">{{ $partido->equipoLocal->nombre }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Local</p>
                            </div>

                            <div class="text-center w-1/3" x-data="{ goles_local: {{ old('goles_local', $partido->goles_local) ?? 0 }}, goles_visitante: {{ old('goles_visitante', $partido->goles_visitante) ?? 0 }} }">
                                <div class="flex justify-center items-center">
                                    <div class="score-input-container">
                                        <span class="score-btn" @click="goles_local = Math.max(0, goles_local - 1)">-</span>
                                        <input type="number" name="goles_local" x-model="goles_local" class="score-input text-gray-900 dark:text-gray-100">
                                        <span class="score-btn" @click="goles_local++">+</span>
                                    </div>
                                    <span class="text-3xl sm:text-5xl font-bold mx-2 sm:mx-4 text-gray-400 dark:text-gray-500">-</span>
                                    <div class="score-input-container">
                                        <span class="score-btn" @click="goles_visitante = Math.max(0, goles_visitante - 1)">-</span>
                                        <input type="number" name="goles_visitante" x-model="goles_visitante" class="score-input text-gray-900 dark:text-gray-100">
                                        <span class="score-btn" @click="goles_visitante++">+</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        <option value="pendiente" {{ $partido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_juego" {{ $partido->estado == 'en_juego' ? 'selected' : '' }}>En Juego</option>
                                        <option value="finalizado" {{ $partido->estado == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="reprogramado" {{ $partido->estado == 'reprogramado' ? 'selected' : '' }}>Reprogramado</option>
                                        <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center w-1/3">
                                <img src="{{ $partido->equipoVisitante->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoVisitante->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo de {{ $partido->equipoVisitante->nombre }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover mx-auto mb-2">
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">{{ $partido->equipoVisitante->nombre }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Visitante</p>
                            </div>
                        </div>
                    </div>

                    <!-- Player Stats Tabs -->
                    <div class="p-4">
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                            <nav class="-mb-px flex justify-center space-x-4 sm:space-x-8" aria-label="Tabs">
                                <button type="button" @click="activeTab = 'local'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'local', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'local' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm sm:text-base">
                                    Estadísticas {{ $partido->equipoLocal->nombre }}
                                </button>
                                <button type="button" @click="activeTab = 'visitante'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'visitante', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'visitante' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm sm:text-base">
                                    Estadísticas {{ $partido->equipoVisitante->nombre }}
                                </button>
                            </nav>
                        </div>

                        <div x-show="activeTab === 'local'">
                            @include('partidos.partials.player-stats-table', ['equipo' => $partido->equipoLocal, 'playerStats' => $playerStats])
                        </div>
                        <div x-show="activeTab === 'visitante'" style="display: none;">
                            @include('partidos.partials.player-stats-table', ['equipo' => $partido->equipoVisitante, 'playerStats' => $playerStats])
                        </div>
                    </div>
                </div>

                <!-- Other Details (Collapsible) -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mt-6" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="w-full p-4 text-left font-bold text-gray-900 dark:text-gray-100 flex justify-between items-center">
                        <span>Más Detalles del Partido</span>
                        <svg class="w-5 h-5 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="fecha_partido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha y Hora</label>
                                <input type="datetime-local" name="fecha_partido" id="fecha_partido" value="{{ old('fecha_partido', \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm">
                            </div>
                            <div>
                                <label for="ubicacion_partido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubicación</label>
                                <input type="text" name="ubicacion_partido" id="ubicacion_partido" value="{{ old('ubicacion_partido', $partido->ubicacion_partido) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm">
                            </div>
                            <div>
                                <label for="equipo_local_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipo Local</label>
                                <select name="equipo_local_id" id="equipo_local_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $localTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipo Visitante</label>
                                <select name="equipo_visitante_id" id="equipo_visitante_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $visitorTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('campeonatos.show', $partido->campeonato) }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Cancelar</a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const localSelect = document.getElementById('equipo_local_id');
        const visitanteSelect = document.getElementById('equipo_visitante_id');
        const existingPairings = @json($existingPairings);

        function getPairKey(team1Id, team2Id) {
            if (!team1Id || !team2Id) return null;
            return [team1Id, team2Id].sort().join('-');
        }

        function updateOptions(sourceSelect, targetSelect) {
            const sourceId = sourceSelect.value;

            for (let option of targetSelect.options) {
                const targetId = option.value;
                
                option.text = option.text.replace(/ \((Ya jugó|Disponible)\)$/, '');
                option.style.color = '';

                if (sourceId && targetId && sourceId !== targetId) {
                    const pairKey = getPairKey(sourceId, targetId);
                    if (existingPairings[pairKey]) {
                        option.text += ' (Ya jugó)';
                        option.style.color = 'red';
                    } else {
                        option.text += ' (Disponible)';
                        option.style.color = 'green';
                    }
                }
            }
        }

        localSelect.addEventListener('change', () => updateOptions(localSelect, visitanteSelect));
        visitanteSelect.addEventListener('change', () => updateOptions(visitanteSelect, localSelect));

        if (localSelect.value) {
            updateOptions(localSelect, visitanteSelect);
        }
        if (visitanteSelect.value) {
            updateOptions(visitanteSelect, localSelect);
        }
    });
    </script>
    @endpush
</x-app-layout>
