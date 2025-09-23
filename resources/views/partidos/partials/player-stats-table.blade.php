<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jugador</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Goles</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asist.</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">T.A.</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">T.R.</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($equipo->jugadores as $jugador)
                @php
                    $stats = $playerStats->get($jugador->id);
                    $isSuspended = $jugador->suspendido;
                @endphp
                <tr class="{{ $isSuspended ? 'opacity-50 bg-gray-100 dark:bg-gray-900 pointer-events-none' : '' }}">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $jugador->nombre }} {{ $jugador->apellido }}</div>
                            @if($isSuspended)
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">Suspendido</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <input type="number" name="jugadores[{{ $jugador->id }}][goles]" value="{{ old('jugadores.' . $jugador->id . '.goles', $stats->goles ?? 0) }}" class="w-16 text-center rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <input type="number" name="jugadores[{{ $jugador->id }}][asistencias]" value="{{ old('jugadores.' . $jugador->id . '.asistencias', $stats->asistencias ?? 0) }}" class="w-16 text-center rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" @if($isSuspended) disabled @endif>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <input type="number" name="jugadores[{{ $jugador->id }}][amarillas]" value="{{ old('jugadores.' . $jugador->id . '.amarillas', $stats->tarjetas_amarillas ?? 0) }}" class="w-16 text-center rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="2" @if($isSuspended) disabled @endif>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <input type="number" name="jugadores[{{ $jugador->id }}][rojas]" value="{{ old('jugadores.' . $jugador->id . '.rojas', $stats->tarjetas_rojas ?? 0) }}" class="w-16 text-center rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm text-sm" min="0" max="1" @if($isSuspended) disabled @endif>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
