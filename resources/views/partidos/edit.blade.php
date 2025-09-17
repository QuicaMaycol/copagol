<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Editar Partido</h2>

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
                            <label for="fecha_partido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha y Hora del Partido</label>
                            <input type="datetime-local" name="fecha_partido" id="fecha_partido" value="{{ old('fecha_partido', \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <!-- Ubicacion -->
                        <div class="mb-4">
                            <label for="ubicacion_partido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubicación del Partido</label>
                            <input type="text" name="ubicacion_partido" id="ubicacion_partido" value="{{ old('ubicacion_partido', $partido->ubicacion_partido) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <!-- Equipo Local -->
                        <div class="mb-4">
                            <label for="equipo_local_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipo Local</label>
                            <select name="equipo_local_id" id="equipo_local_id" class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ in_array($localTeamId, $conflictingTeams) ? 'border-red-500' : 'border-gray-300' }} dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $localTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Equipo Visitante -->
                        <div class="mb-4">
                            <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipo Visitante</label>
                            <select name="equipo_visitante_id" id="equipo_visitante_id" class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ in_array($visitorTeamId, $conflictingTeams) ? 'border-red-500' : 'border-gray-300' }} dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $visitorTeamId == $team->id ? 'selected' : '' }}>{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Goles del Partido -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="goles_local" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Goles {{ $partido->equipoLocal->nombre }}</label>
                                <input type="number" name="goles_local" id="goles_local" value="{{ old('goles_local', $partido->goles_local) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0">
                            </div>
                            <div>
                                <label for="goles_visitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Goles {{ $partido->equipoVisitante->nombre }}</label>
                                <input type="number" name="goles_visitante" id="goles_visitante" value="{{ old('goles_visitante', $partido->goles_visitante) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" min="0">
                            </div>
                        </div>

                        <!-- Estadísticas del Equipo Local -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg shadow" x-data="{ search: '' }">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Estadísticas {{ $partido->equipoLocal->nombre }}</h3>
                            <input type="text" x-model="search" placeholder="Buscar por nombre, apellido o n° de camiseta..." class="mb-4 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                            @foreach($partido->equipoLocal->jugadores as $jugador)
                                @php
                                    $stats = $playerStats->get($jugador->id);
                                    $isSuspended = $jugador->suspendido;
                                    $rowClasses = 'grid grid-cols-1 md:grid-cols-6 gap-4 mb-3 items-center border-b border-gray-200 dark:border-gray-700 pb-3 transition-all duration-300';
                                    if ($isSuspended) {
                                        $rowClasses .= ' opacity-50 bg-gray-100 dark:bg-gray-900 pointer-events-none';
                                    }
                                @endphp
                                <div class="{{ $rowClasses }}" x-show="search === '' || '{{ strtolower($jugador->nombre . ' ' . $jugador->apellido . ' ' . $jugador->numero_camiseta) }}'.includes(search.toLowerCase())">
                                    <div class="col-span-2 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $jugador->numero_camiseta ? '#' . $jugador->numero_camiseta . ' - ' : '' }}{{ $jugador->nombre }} {{ $jugador->apellido }}
                                        @if($isSuspended)
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">
                                                Suspendido
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_goles" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Goles</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][goles]" id="jugador_{{ $jugador->id }}_goles" value="{{ old('jugadores.' . $jugador->id . '.goles', $stats->goles ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_asistencias" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Asistencias</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" id="jugador_{{ $jugador->id }}_asistencias" value="{{ old('jugadores.' . $jugador->id . '.asistencias', $stats->asistencias ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_amarillas" class="block text-xs font-medium text-gray-500 dark:text-gray-400">T. Amarillas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][amarillas]" id="jugador_{{ $jugador->id }}_amarillas" value="{{ old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="2" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_rojas" class="block text-xs font-medium text-gray-500 dark:text-gray-400">T. Roja</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][rojas]" id="jugador_{{ $jugador->id }}_rojas" value="{{ old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="1" @if($isSuspended) disabled @endif>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estadísticas del Equipo Visitante -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg shadow" x-data="{ search: '' }">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Estadísticas {{ $partido->equipoVisitante->nombre }}</h3>
                            <input type="text" x-model="search" placeholder="Buscar por nombre, apellido o n° de camiseta..." class="mb-4 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                            @foreach($partido->equipoVisitante->jugadores as $jugador)
                                @php
                                    $stats = $playerStats->get($jugador->id);
                                    $isSuspended = $jugador->suspendido;
                                    $rowClasses = 'grid grid-cols-1 md:grid-cols-6 gap-4 mb-3 items-center border-b border-gray-200 dark:border-gray-700 pb-3 transition-all duration-300';
                                    if ($isSuspended) {
                                        $rowClasses .= ' opacity-50 bg-gray-100 dark:bg-gray-900 pointer-events-none';
                                    }
                                @endphp
                                <div class="{{ $rowClasses }}" x-show="search === '' || '{{ strtolower($jugador->nombre . ' ' . $jugador->apellido . ' ' . $jugador->numero_camiseta) }}'.includes(search.toLowerCase())">
                                    <div class="col-span-2 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $jugador->numero_camiseta ? '#' . $jugador->numero_camiseta . ' - ' : '' }}{{ $jugador->nombre }} {{ $jugador->apellido }}
                                        @if($isSuspended)
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">
                                                Suspendido
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_goles" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Goles</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][goles]" id="jugador_{{ $jugador->id }}_goles" value="{{ old('jugadores.' . $jugador->id . '.goles', $stats->goles ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_asistencias" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Asistencias</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" id="jugador_{{ $jugador->id }}_asistencias" value="{{ old('jugadores.' . $jugador->id . '.asistencias', $stats->asistencias ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_amarillas" class="block text-xs font-medium text-gray-500 dark:text-gray-400">T. Amarillas</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][amarillas]" id="jugador_{{ $jugador->id }}_amarillas" value="{{ old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="2" @if($isSuspended) disabled @endif>
                                    </div>
                                    <div>
                                        <label for="jugador_{{ $jugador->id }}_rojas" class="block text-xs font-medium text-gray-500 dark:text-gray-400">T. Roja</label>
                                        <input type="number" name="jugadores[{{ $jugador->id }}][rojas]" id="jugador_{{ $jugador->id }}_rojas" value="{{ old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="1" @if($isSuspended) disabled @endif>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado del Partido</label>
                            <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="pendiente" {{ $partido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_juego" {{ $partido->estado == 'en_juego' ? 'selected' : '' }}>En Juego</option>
                                <option value="finalizado" {{ $partido->estado == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                <option value="reprogramado" {{ $partido->estado == 'reprogramado' ? 'selected' : '' }}>Reprogramado</option>
                                <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('campeonatos.show', $partido->campeonato) }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white mr-4">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>