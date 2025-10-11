<div class="overflow-x-auto bg-gray-800">
    <table class="min-w-full">
        <thead class="bg-gray-700/50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Jugador</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Goles</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Asist.</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">T.A.</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">T.R.</th>
            </tr>
        </thead>
        <tbody class="bg-gray-800 divide-y divide-gray-700">
            @forelse($equipo->jugadores as $jugador)
                @php
                    $stats = $playerStats->get($jugador->id);
                    $isSuspended = $jugador->suspendido;
                @endphp
                <tr class="{{ $isSuspended ? 'opacity-40' : '' }}">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-200">[{{ $jugador->numero_camiseta }}] {{ $jugador->nombre }} {{ $jugador->apellido }}</div>
                            @if($isSuspended)
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">Suspendido</span>
                            @endif
                        </div>
                    </td>
                    @foreach(['goles', 'asistencias', 'amarillas', 'rojas'] as $statName)
                        @php
                            $statValue = 0;
                            if ($statName === 'amarillas') {
                                $statValue = old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0);
                            } elseif ($statName === 'rojas') {
                                $statValue = old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0);
                            } else {
                                $statValue = old('jugadores.' . $jugador->id . '.' . $statName, $stats->$statName ?? 0);
                            }
                            $max = ($statName === 'amarillas') ? 2 : (($statName === 'rojas') ? 1 : null);
                        @endphp
                        <td class="px-2 py-2 whitespace-nowrap">
                            <input type="number" name="jugadores[{{ $jugador->id }}][{{$statName}}]" value="{{ $statValue }}"
                                   class="w-16 text-center rounded-md border-gray-600 bg-gray-700 text-gray-200 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   min="0" {{ $max ? 'max='.$max : '' }} @if($isSuspended) disabled @endif>
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">
                        No hay jugadores en este equipo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>