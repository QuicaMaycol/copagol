<x-guest-layout>
    @php
    // Particionar todos los partidos en finalizados y próximos
    [$partidosFinalizados, $partidosProximos] = $campeonato->partidos->partition(function ($partido) {
        return $partido->estado === 'finalizado';
    });

    // Filtrar partidos de fase de grupos y agrupar por jornada
    $finalizadosPorJornada = $partidosFinalizados
        ->filter(function ($partido) {
            return optional($partido->fase)->tipo !== 'eliminatoria';
        })
        ->sortBy('jornada')
        ->groupBy('jornada');

    $proximosPorJornada = $partidosProximos
        ->filter(function ($partido) {
            return optional($partido->fase)->tipo !== 'eliminatoria';
        })
        ->sortBy('jornada')
        ->groupBy('jornada');
@endphp

    <div class="bg-gray-900 text-gray-300 font-sans relative min-h-screen">
        @guest
        <div class="absolute top-4 right-4 flex space-x-2 z-10">
            <a href="{{ route('login') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">Registrarse</a>
        </div>
        @endguest
        <div class="container mx-auto p-4">
            <div class="text-center mb-8 pt-8">
                <h1 class="text-4xl font-bold text-white">{{ $campeonato->nombre_campeonato }}</h1>
                <p class="text-md text-gray-400">Organizado por {{ $campeonato->organizador->name }}</p>
                <div class="mt-4">
                    <a href="{{ route('campeonatos.imprimirPadron', $campeonato) }}" target="_blank" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                        Imprimir Padrón de Jugadores
                    </a>
                </div>
            </div>

            <!-- Featured Match -->
            @if($featuredMatch)
                <div class="max-w-4xl mx-auto mb-8">
                    <a href="{{ route('partidos.public_show', ['partido' => $featuredMatch->id]) }}" class="block bg-gray-800 rounded-lg shadow-lg p-6 hover:bg-gray-700/60 transition-colors duration-200">
                        <div class="text-center text-sm font-semibold text-indigo-400 mb-2">
                            <span>PRÓXIMO PARTIDO &middot; Jornada {{ $featuredMatch->jornada }}</span>
                        </div>
                        <div class="flex items-center justify-around text-xl md:text-2xl font-bold">
                            <div class="flex-1 text-center">
                                <img src="{{ $featuredMatch->equipoLocal->imagen_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($featuredMatch->equipoLocal->nombre) }}" alt="{{ $featuredMatch->equipoLocal->nombre }}" class="w-16 h-16 mx-auto mb-2 rounded-full object-cover border-2 border-gray-600">
                                <h3 class="text-gray-200">{{ $featuredMatch->equipoLocal->nombre }}</h3>
                            </div>
                            <div class="w-24 text-center font-extrabold text-3xl md:text-4xl mx-2 text-gray-200">
                                <span>{{ \Carbon\Carbon::parse($featuredMatch->fecha_partido)->format('H:i') }}</span>
                                <div class="text-sm font-normal text-gray-500">{{ \Carbon\Carbon::parse($featuredMatch->fecha_partido)->format('d M') }}</div>
                            </div>
                            <div class="flex-1 text-center">
                                <img src="{{ $featuredMatch->equipoVisitante->imagen_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($featuredMatch->equipoVisitante->nombre) }}" alt="{{ $featuredMatch->equipoVisitante->nombre }}" class="w-16 h-16 mx-auto mb-2 rounded-full object-cover border-2 border-gray-600">
                                <h3 class="text-gray-200">{{ $featuredMatch->equipoVisitante->nombre }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Pestañas para Próximos y Resultados -->
            <div x-data="{ activeTab: 'proximos' }" class="w-full max-w-4xl mx-auto">
                <div class="flex justify-center border-b-2 border-gray-700 mb-4">
                    <button @click="activeTab = 'proximos'" 
                            :class="{'border-indigo-500 text-white': activeTab === 'proximos', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== 'proximos'}"
                            class="py-2 px-4 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                        Próximos
                    </button>
                    <button @click="activeTab = 'resultados'" 
                            :class="{'border-indigo-500 text-white': activeTab === 'resultados', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== 'resultados'}"
                            class="py-2 px-4 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                        Resultados
                    </button>
                </div>

                <!-- Contenido de Partidos Próximos -->
                <div x-show="activeTab === 'proximos'" x-cloak>
                    @php
                        $playoffFases = $campeonato->fases ? $campeonato->fases->where('tipo', 'eliminatoria')->sortBy('orden') : collect();
                        $totalProximosPlayoffs = 0;
                        if ($playoffFases->count() > 0) {
                            foreach ($playoffFases as $fase) {
                                $totalProximosPlayoffs += $fase->partidos->where('estado', '!=', 'finalizado')->count();
                            }
                        }
                    @endphp

                    @if($proximosPorJornada->isEmpty() && $totalProximosPlayoffs === 0)
                        <p class="text-center text-gray-500 p-4">No hay próximos partidos programados.</p>
                    @else
                        <div x-data="{ openJornada: {{ $proximosPorJornada->keys()->first() ?? 'null' }} }">
                            @foreach($proximosPorJornada as $jornada => $matches)
                                @include('campeonatos.partials._jornada_accordion', ['jornada' => $jornada, 'matches' => $matches, 'campeonato' => $campeonato, 'isResultados' => false])
                            @endforeach
                        </div>

                        @if($totalProximosPlayoffs > 0)
                            <div class="mt-8 bg-gray-800 rounded-lg shadow-md">
                                <div class="p-4 bg-gray-700/50 text-white font-bold text-lg rounded-t-lg flex justify-between items-center">
                                    <h2>Fase Eliminatoria: Próximos</h2>
                                </div>
                                <div class="p-0">
                                    @foreach($playoffFases as $fase)
                                        @php
                                            $partidosFase = $fase->partidos->where('estado', '!=', 'finalizado')->sortBy('fecha_partido');
                                        @endphp
                                        @if($partidosFase->count() > 0)
                                            <div class="py-3 bg-gray-800/50 rounded-lg mb-4">
                                                <div class="px-4 flex items-center justify-between mb-3">
                                                    <h3 class="text-base font-semibold text-gray-200">{{ $fase->nombre }}</h3>
                                                </div>
                                                @php
                                                    $partidosAgrupados = $partidosFase->groupBy(function($partido) {
                                                        $fecha = $partido->fecha_partido ? \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d') : 'sin_fecha';
                                                        $ubicacion = $partido->ubicacion_partido ?? 'sin_ubicacion';
                                                        return $fecha . '|' . $ubicacion;
                                                    });
                                                @endphp

                                                @foreach($partidosAgrupados as $grupoKey => $partidosDelGrupo)
                                                    @php
                                                        [$fecha, $ubicacion] = explode('|', $grupoKey);
                                                    @endphp
                                                    <div class="mt-3">
                                                        <div class="px-4 pb-2">
                                                            <div class="flex justify-between items-center text-xs text-gray-400 border-b border-gray-700 pb-2">
                                                                <span class="flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                                    {{ $fecha !== 'sin_fecha' ? \Carbon\Carbon::parse($fecha)->translatedFormat('D, d M Y') : 'Fecha por definir' }}
                                                                </span>
                                                                <span class="text-right flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                                    {{ $ubicacion !== 'sin_ubicacion' ? $ubicacion : 'Ubicación por definir' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="px-4 pt-2 space-y-2">
                                                            @foreach($partidosDelGrupo as $partido)
                                                                <a href="{{ route('partidos.public_show', ['partido' => $partido->id]) }}" class="block rounded-lg bg-gray-900 hover:bg-gray-700/80 transition-colors">
                                                                    <div class="p-3">
                                                                        <div class="grid grid-cols-3 items-center gap-2">
                                                                            <div class="text-left font-semibold text-gray-100 leading-tight break-words flex items-center">
                                                                                <img src="{{ optional($partido->equipoLocal)->imagen_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional($partido->equipoLocal)->nombre ?? 'TBD') }}" alt="{{ optional($partido->equipoLocal)->nombre ?? 'TBD' }}" class="w-6 h-6 mr-2 rounded-full object-cover border-2 border-gray-600">
                                                                                <span>{{ optional($partido->equipoLocal)->nombre ?? 'TBD' }}</span>
                                                                            </div>
                                                                            <div class="text-center">
                                                                                @php
                                                                                    $isLiveOrSuspended = in_array($partido->estado, ['en_curso', 'suspendido']);
                                                                                    $hasScore = !is_null($partido->goles_local) && !is_null($partido->goles_visitante);
                                                                                @endphp

                                                                                @if($isLiveOrSuspended && $hasScore)
                                                                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-gray-200 text-gray-900 font-extrabold text-md">
                                                                                        {{ $partido->goles_local }} : {{ $partido->goles_visitante }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-xl font-bold text-gray-100">
                                                                                        {{ $partido->fecha_partido ? \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') : '-:-' }}
                                                                                    </span>
                                                                                @endif

                                                                                <div class="text-[10px] uppercase mt-1 font-semibold">
                                                                                    @switch($partido->estado)
                                                                                        @case('suspendido') <span class="text-yellow-400">Suspendido</span> @break
                                                                                        @case('en_curso') <span class="text-red-500 animate-pulse">En vivo</span> @break
                                                                                        @default <span class="text-gray-400">Programado</span>
                                                                                    @endswitch
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-right font-semibold text-gray-100 leading-tight break-words flex items-center justify-end">
                                                                                <span>{{ optional($partido->equipoVisitante)->nombre ?? 'TBD' }}</span>
                                                                                <img src="{{ optional($partido->equipoVisitante)->imagen_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional($partido->equipoVisitante)->nombre ?? 'TBD') }}" alt="{{ optional($partido->equipoVisitante)->nombre ?? 'TBD' }}" class="w-6 h-6 ml-2 rounded-full object-cover border-2 border-gray-600">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Contenido de Resultados -->
                <div x-show="activeTab === 'resultados'" x-cloak>
                    <div x-data="{ openJornada: {{ $finalizadosPorJornada->keys()->first() ?? 'null' }} }">
                        @forelse($finalizadosPorJornada as $jornada => $matches)
                            @include('campeonatos.partials._jornada_accordion', ['jornada' => $jornada, 'matches' => $matches, 'campeonato' => $campeonato, 'isResultados' => true])
                        @empty
                            <p class="text-center text-gray-500 p-4">No hay resultados de partidos de fase de grupos todavía.</p>
                        @endforelse
                    </div>

                    {{-- Fase Eliminatoria: Resultados --}}
                    @php
                        $playoffFasesResultados = $campeonato->fases ? $campeonato->fases->where('tipo', 'eliminatoria')->sortBy('orden') : collect();
                        $totalResultadosPlayoffs = 0;
                        if ($playoffFasesResultados->count() > 0) {
                            foreach ($playoffFasesResultados as $fase) {
                                $totalResultadosPlayoffs += $fase->partidos->where('estado', 'finalizado')->count();
                            }
                        }
                    @endphp

                    @if($totalResultadosPlayoffs > 0)
                        <div class="mt-8 bg-gray-800 rounded-lg shadow-md">
                            <div class="p-4 bg-gray-700/50 text-white font-bold text-lg rounded-t-lg flex justify-between items-center">
                                <h2>Fase Eliminatoria: Resultados</h2>
                            </div>
                            <div class="p-0 divide-y divide-gray-700">
                                @foreach($playoffFasesResultados as $fase)
                                    @php
                                        $partidosFase = $fase->partidos->where('estado', 'finalizado')->sortByDesc('fecha_partido');
                                    @endphp
                                    @if($partidosFase->count() > 0)
                                        <div class="px-4 py-3 bg-gray-900/30">
                                            <div class="flex items-center justify-between mb-2">
                                                <h3 class="text-base font-semibold text-gray-200">{{ $fase->nombre }}</h3>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($partidosFase as $partido)
                                                    <a href="{{ route('partidos.public_show', ['partido' => $partido->id]) }}" class="block rounded-lg bg-gray-900 hover:bg-gray-800 transition-colors">
                                                        <div class="p-4">
                                                            <div class="flex justify-between text-xs text-gray-400 mb-2">
                                                                <span>{{ $partido->fecha_partido ? \Carbon\Carbon::parse($partido->fecha_partido)->translatedFormat('ddd. DD MMM') : 'Fecha no disponible' }}</span>
                                                                <span class="text-right">{{ $partido->ubicacion_partido ? 'Campo deportivo - ' . $partido->ubicacion_partido : 'Ubicación no disponible' }}</span>
                                                            </div>
                                                            <div class="grid grid-cols-3 items-center">
                                                                <div class="text-left font-semibold text-gray-100 leading-tight break-words">
                                                                    {{ optional($partido->equipoLocal)->nombre ?? 'TBD' }}
                                                                </div>
                                                                <div class="text-center">
                                                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-gray-200 text-gray-900 font-extrabold text-lg">
                                                                        {{ $partido->goles_local ?? '0' }} : {{ $partido->goles_visitante ?? '0' }}
                                                                    </span>
                                                                    <div class="text-[10px] uppercase mt-1 text-green-400">
                                                                        Finalizado
                                                                    </div>
                                                                </div>
                                                                <div class="text-right font-semibold text-gray-100 leading-tight break-words">
                                                                    {{ optional($partido->equipoVisitante)->nombre ?? 'TBD' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tablas de Posiciones y Goleadores -->
            <div class="w-full max-w-4xl mx-auto mt-10">
                <!-- Tabla de Posiciones -->
                <div x-data="{ open: true }" class="bg-gray-800 rounded-lg shadow-md mb-6">
                    <div @click="open = !open" class="p-4 bg-gray-700/50 text-white font-bold text-lg rounded-t-lg cursor-pointer flex justify-between items-center">
                        <h2>Tabla de Posiciones</h2>
                        <span x-text="open ? '−' : '+'" class="text-xl"></span>
                    </div>
                    <div x-show="open" x-transition class="p-0">
                        @if(!empty($tablaPosiciones))
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-700 text-gray-300 uppercase text-sm leading-normal">
                                            <th class="py-3 px-4 text-left">Pos</th>
                                            <th class="py-3 px-4 text-left">Equipo</th>
                                            <th class="py-3 px-4 text-center">Pts</th>
                                            <th class="py-3 px-4 text-center">PJ</th>
                                            <th class="py-3 px-4 text-center">G</th>
                                            <th class="py-3 px-4 text-center">E</th>
                                            <th class="py-3 px-4 text-center">P</th>
                                            <th class="py-3 px-4 text-center">GF</th>
                                            <th class="py-3 px-4 text-center">GC</th>
                                            <th class="py-3 px-4 text-center">DG</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-300 text-sm font-light">
                                        @foreach($tablaPosiciones as $index => $team)
                                            <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                                <td class="py-3 px-4 text-left font-semibold">{{ $index + 1 }}</td>
                                                <td class="py-3 px-4 text-left">
                                                    <a href="{{ route('equipos.public_show', ['equipo' => $team['id']]) }}" class="hover:underline text-indigo-400">
                                                        {{ $team['nombre'] }}
                                                    </a>
                                                </td>
                                                <td class="py-3 px-4 text-center font-bold text-white">{{ $team['Pts'] }}</td>
                                                <td class="py-3 px-4 text-center">{{ $team['PJ'] }}</td>
                                                <td class="py-3 px-4 text-center text-green-400">{{ $team['PG'] }}</td>
                                                <td class="py-3 px-4 text-center text-yellow-400">{{ $team['PE'] }}</td>
                                                <td class="py-3 px-4 text-center text-red-400">{{ $team['PP'] }}</td>
                                                <td class="py-3 px-4 text-center">{{ $team['GF'] }}</td>
                                                <td class="py-3 px-4 text-center">{{ $team['GC'] }}</td>
                                                <td class="py-3 px-4 text-center">{{ $team['DG'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 p-4">La tabla de posiciones aún no está disponible.</p>
                        @endif
                    </div>
                </div>

                <!-- Máximos Goleadores -->
                <div x-data="{ open: true }" class="bg-gray-800 rounded-lg shadow-md">
                    <div @click="open = !open" class="p-4 bg-gray-700/50 text-white font-bold text-lg rounded-t-lg cursor-pointer flex justify-between items-center">
                        <h2>Máximos Goleadores</h2>
                        <span x-text="open ? '−' : '+'" class="text-xl"></span>
                    </div>
                    <div x-show="open" x-transition class="p-0">
                        @if(!empty($goleadores))
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-700 text-gray-300 uppercase text-sm leading-normal">
                                            <th class="py-3 px-4 text-left">Jugador</th>
                                            <th class="py-3 px-4 text-left">Equipo</th>
                                            <th class="py-3 px-4 text-center">Goles</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-300 text-sm font-light">
                                        @foreach($goleadores as $goleador)
                                            <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                                <td class="py-3 px-4 text-left">{{ $goleador->nombre }} {{ $goleador->apellido }}</td>
                                                <td class="py-3 px-4 text-left">{{ $goleador->equipo->nombre }}</td>
                                                <td class="py-3 px-4 text-center font-bold text-white">{{ $goleador->goles }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 p-4">Aún no hay goleadores registrados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
