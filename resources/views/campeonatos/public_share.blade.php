<x-guest-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $campeonato->nombre_campeonato }}</h1>
        <p class="text-gray-600 mb-6">Organizado por {{ $campeonato->organizador->name }}</p>

        <h2 class="text-2xl font-semibold text-gray-700 mb-3">Tabla de Posiciones</h2>
        @if(!empty($tablaPosiciones))
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="px-4 py-2 text-left">Pos</th>
                            <th class="px-4 py-2 text-left">Equipo</th>
                            <th class="px-4 py-2 text-center">Pts</th>
                            <th class="px-4 py-2 text-center">PJ</th>
                            <th class="px-4 py-2 text-center">PG</th>
                            <th class="px-4 py-2 text-center">PE</th>
                            <th class="px-4 py-2 text-center">PP</th>
                            <th class="px-4 py-2 text-center">GF</th>
                            <th class="px-4 py-2 text-center">GC</th>
                            <th class="px-4 py-2 text-center">DG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablaPosiciones as $index => $team)
                            <tr class="{{ $loop->even ? 'bg-gray-100' : 'bg-white' }}">
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $team['nombre'] }}</td>
                                <td class="px-4 py-2 text-center font-bold">{{ $team['Pts'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['PJ'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['PG'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['PE'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['PP'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['GF'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['GC'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $team['DG'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 mb-6">La tabla de posiciones aún no está disponible.</p>
        @endif

        <h2 class="text-2xl font-semibold text-gray-700 mb-3">Máximos Goleadores</h2>
        @if(!empty($goleadores))
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="px-4 py-2 text-left">Jugador</th>
                            <th class="px-4 py-2 text-left">Equipo</th>
                            <th class="px-4 py-2 text-center">Goles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goleadores as $goleador)
                            <tr class="{{ $loop->even ? 'bg-gray-100' : 'bg-white' }}">
                                <td class="px-4 py-2">{{ $goleador->nombre }} {{ $goleador->apellido }}</td>
                                <td class="px-4 py-2">{{ $goleador->equipo->nombre }}</td>
                                <td class="px-4 py-2 text-center font-bold">{{ $goleador->goles }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 mb-6">Aún no hay goleadores registrados.</p>
        @endif

        <h2 class="text-2xl font-semibold text-gray-700 mb-3">Partidos</h2>
        @php
            $matchesByJornada = $campeonato->partidos->sortBy('jornada')->groupBy('jornada');
        @endphp

        @forelse($matchesByJornada as $jornada => $matches)
            <div class="mb-4 p-4 bg-white rounded-lg shadow">
                <h3 class="text-xl font-bold text-gray-800 mb-3">Jornada {{ $jornada }}</h3>
                @php
                    $allTeamIds = $campeonato->equipos->pluck('id')->toArray();
                    $playingTeamIds = $matches->pluck('equipo_local_id')->concat($matches->pluck('equipo_visitante_id'))->unique()->toArray();
                    $restingTeamIds = array_diff($allTeamIds, $playingTeamIds);
                    $restingTeamName = null;
                    if (!empty($restingTeamIds)) {
                        $restingTeam = \App\Models\Equipo::find(reset($restingTeamIds));
                        if ($restingTeam) {
                            $restingTeamName = $restingTeam->nombre;
                        }
                    }
                @endphp
                @if($restingTeamName)
                    <div class="bg-blue-100 text-blue-800 p-3 rounded-lg mb-4 text-center font-semibold">
                        Equipo que descansa: {{ $restingTeamName }}
                    </div>
                @endif
                @foreach($matches->sortBy('fecha_partido') as $match)
                    <div class="flex justify-between items-center p-3 mb-2 bg-gray-50 rounded-lg">
                        <div class="flex-1 text-right font-semibold">{{ $match->equipoLocal->nombre }}</div>
                        <div class="mx-4 text-lg font-bold">
                            @if($match->estado === 'finalizado')
                                {{ $match->goles_local }} - {{ $match->goles_visitante }}
                            @else
                                vs
                            @endif
                        </div>
                        <div class="flex-1 text-left font-semibold">{{ $match->equipoVisitante->nombre }}</div>
                    </div>
                    <p class="text-center text-sm text-gray-500 mb-2">{{ \Carbon\Carbon::parse($match->fecha_partido)->format('d M Y H:i') }} - {{ $match->ubicacion_partido ?? 'Sin Ubicación' }}</p>
                @endforeach
            </div>
        @empty
            <p class="text-gray-500">No hay partidos programados para este campeonato.</p>
        @endforelse
    </div>
</x-guest-layout>