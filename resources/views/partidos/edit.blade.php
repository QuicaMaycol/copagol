<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Editar Partido</h2>

                    @if ($errors->has('general'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ $errors->first('general') }}</span>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Advertencia!</strong>
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    @php
                        $conflictingTeams = session('conflicting_teams', []);
                        $localTeamId = old('equipo_local_id', $partido->equipo_local_id);
                        $visitorTeamId = old('equipo_visitante_id', $partido->equipo_visitante_id);
                    @endphp

                    <form action="{{ route('partidos.update', $partido) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Fecha y Hora -->
                        <div class="mb-4">
                            <label for="fecha_partido" class="block text-sm font-medium text-gray-700">Fecha y Hora del Partido</label>
                            <input type="datetime-local" name="fecha_partido" id="fecha_partido" value="{{ old('fecha_partido', \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <!-- Ubicacion -->
                        <div class="mb-4">
                            <label for="ubicacion_partido" class="block text-sm font-medium text-gray-700">Ubicación del Partido</label>
                            <input type="text" name="ubicacion_partido" id="ubicacion_partido" value="{{ old('ubicacion_partido', $partido->ubicacion_partido) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <!-- Equipo Local -->
                        <div class="mb-4">
                            <label for="equipo_local_id" class="block text-sm font-medium text-gray-700">Equipo Local</label>
                            <select name="equipo_local_id" id="equipo_local_id" class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ in_array($localTeamId, $conflictingTeams) ? 'border-red-500' : 'border-gray-300' }}">
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $localTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Equipo Visitante -->
                        <div class="mb-4">
                            <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-700">Equipo Visitante</label>
                            <select name="equipo_visitante_id" id="equipo_visitante_id" class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ in_array($visitorTeamId, $conflictingTeams) ? 'border-red-500' : 'border-gray-300' }}">
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $visitorTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Goles del Partido -->
                        <div class="mb-4">
                            <label for="goles_local" class="block text-sm font-medium text-gray-700">Goles {{ $partido->equipoLocal->nombre }}</label>
                            <input type="number" name="goles_local" id="goles_local" value="{{ old('goles_local', $partido->goles_local) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0">
                        </div>

                        <div class="mb-4">
                            <label for="goles_visitante" class="block text-sm font-medium text-gray-700">Goles {{ $partido->equipoVisitante->nombre }}</label>
                            <input type="number" name="goles_visitante" id="goles_visitante" value="{{ old('goles_visitante', $partido->goles_visitante) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0">
                        </div>

                        <!-- Estadísticas del Equipo Local -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas {{ $partido->equipoLocal->nombre }}</h3>
                            @foreach($partido->equipoLocal->jugadores as $jugador)
                                @php
                                    $stats = $playerStats->get($jugador->id);
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-3 items-center border-b pb-3">
                                    <div class="col-span-2 font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_goles" class="block text-xs font-medium text-gray-500">Goles</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][goles]" id="jugador_{{ $jugador->id }}_goles" value="{{ old('jugadores.' . $jugador->id . '.goles', $stats->goles ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_asistencias" class="block text-xs font-medium text-gray-500">Asistencias</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" id="jugador_{{ $jugador->id }}_asistencias" value="{{ old('jugadores.' . $jugador->id . '.asistencias', $stats->asistencias ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_amarillas" class="block text-xs font-medium text-gray-500">Amarillas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][amarillas]" id="jugador_{{ $jugador->id }}_amarillas" value="{{ old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_rojas" class="block text-xs font-medium text-gray-500">Rojas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][rojas]" id="jugador_{{ $jugador->id }}_rojas" value="{{ old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estadísticas del Equipo Visitante -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas {{ $partido->equipoVisitante->nombre }}</h3>
                            @foreach($partido->equipoVisitante->jugadores as $jugador)
                                @php
                                    $stats = $playerStats->get($jugador->id);
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-3 items-center border-b pb-3">
                                    <div class="col-span-2 font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_goles" class="block text-xs font-medium text-gray-500">Goles</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][goles]" id="jugador_{{ $jugador->id }}_goles" value="{{ old('jugadores.' . $jugador->id . '.goles', $stats->goles ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_asistencias" class="block text-xs font-medium text-gray-500">Asistencias</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" id="jugador_{{ $jugador->id }}_asistencias" value="{{ old('jugadores.' . $jugador->id . '.asistencias', $stats->asistencias ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_amarillas" class="block text-xs font-medium text-gray-500">Amarillas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][amarillas]" id="jugador_{{ $jugador->id }}_amarillas" value="{{ old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_rojas" class="block text-xs font-medium text-gray-500">Rojas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][rojas]" id="jugador_{{ $jugador->id }}_rojas" value="{{ old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado del Partido</label>
                            <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="pendiente" {{ $partido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_juego" {{ $partido->estado == 'en_juego' ? 'selected' : '' }}>En Juego</option>
                                <option value="finalizado" {{ $partido->estado == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                <option value="reprogramado" {{ $partido->estado == 'reprogramado' ? 'selected' : '' }}>Reprogramado</option>
                                <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('campeonatos.show', $partido->campeonato) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>