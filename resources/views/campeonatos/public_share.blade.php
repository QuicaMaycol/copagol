<x-guest-layout>
    @php
        // Particionar partidos en finalizados y próximos
        [$partidosFinalizados, $partidosProximos] = $campeonato->partidos->partition(function ($partido) {
            return $partido->estado === 'finalizado';
        });

        // Agrupar por jornada
        $finalizadosPorJornada = $partidosFinalizados->sortBy('jornada')->groupBy('jornada');
        $proximosPorJornada = $partidosProximos->sortBy('jornada')->groupBy('jornada');
    @endphp

    <div class="container mx-auto p-4 bg-gray-50 font-sans">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">{{ $campeonato->nombre_campeonato }}</h1>
            <p class="text-md text-gray-600">Organizado por {{ $campeonato->organizador->name }}</p>
        </div>

        <!-- Featured Match -->
        @if($featuredMatch)
            <div class="max-w-4xl mx-auto mb-8">
                <a href="{{ route('partidos.public_show', ['partido' => $featuredMatch->id]) }}" class="block bg-white rounded-lg shadow-lg p-6 hover:bg-gray-100 transition-colors duration-200">
                    <div class="text-center text-sm font-semibold text-blue-600 mb-2">
                        <span>PRÓXIMO PARTIDO &middot; Jornada {{ $featuredMatch->jornada }}</span>
                    </div>
                    <div class="flex items-center justify-around text-xl md:text-2xl font-bold">
                        <div class="flex-1 text-center">
                            <img src="{{ $featuredMatch->equipoLocal->imagen_url ?? 'http://static.photos/sport/80x80/' . $featuredMatch->equipoLocal->id }}" alt="{{ $featuredMatch->equipoLocal->nombre }}" class="w-16 h-16 mx-auto mb-2 rounded-full object-cover">
                            <h3 class="text-gray-800">{{ $featuredMatch->equipoLocal->nombre }}</h3>
                        </div>
                        <div class="w-24 text-center font-extrabold text-3xl md:text-4xl mx-2 text-gray-700">
                            <span>{{ \Carbon\Carbon::parse($featuredMatch->fecha_partido)->format('H:i') }}</span>
                            <div class="text-sm font-normal text-gray-500">{{ \Carbon\Carbon::parse($featuredMatch->fecha_partido)->format('d M') }}</div>
                        </div>
                        <div class="flex-1 text-center">
                            <img src="{{ $featuredMatch->equipoVisitante->imagen_url ?? 'http://static.photos/sport/80x80/' . $featuredMatch->equipoVisitante->id }}" alt="{{ $featuredMatch->equipoVisitante->nombre }}" class="w-16 h-16 mx-auto mb-2 rounded-full object-cover">
                            <h3 class="text-gray-800">{{ $featuredMatch->equipoVisitante->nombre }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Pestañas para Próximos y Resultados -->
        <div x-data="{ activeTab: 'proximos' }" class="w-full max-w-4xl mx-auto">
            <div class="flex justify-center border-b-2 border-gray-200 mb-4">
                <button @click="activeTab = 'proximos'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'proximos', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'proximos'}"
                        class="py-2 px-4 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                    Próximos
                </button>
                <button @click="activeTab = 'resultados'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'resultados', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'resultados'}"
                        class="py-2 px-4 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                    Resultados
                </button>
            </div>

            <!-- Contenido de Partidos Próximos -->
            <div x-show="activeTab === 'proximos'" x-cloak>
                <div x-data="{ openJornada: {{ $proximosPorJornada->keys()->first() ?? 'null' }} }">
                    @forelse($proximosPorJornada as $jornada => $matches)
                        @include('campeonatos.partials._jornada_accordion', ['jornada' => $jornada, 'matches' => $matches, 'campeonato' => $campeonato, 'isResultados' => false])
                    @empty
                        <p class="text-center text-gray-500 p-4">No hay próximos partidos programados.</p>
                    @endforelse
                </div>
            </div>

            <!-- Contenido de Resultados -->
            <div x-show="activeTab === 'resultados'" x-cloak>
                <div x-data="{ openJornada: {{ $finalizadosPorJornada->keys()->first() ?? 'null' }} }">
                    @forelse($finalizadosPorJornada as $jornada => $matches)
                        @include('campeonatos.partials._jornada_accordion', ['jornada' => $jornada, 'matches' => $matches, 'campeonato' => $campeonato, 'isResultados' => true])
                    @empty
                        <p class="text-center text-gray-500 p-4">No hay resultados de partidos todavía.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tablas de Posiciones y Goleadores -->
        <div class="w-full max-w-4xl mx-auto mt-10">
            <!-- Tabla de Posiciones -->
            <div x-data="{ open: true }" class="bg-white rounded-lg shadow-md mb-6">
                <div @click="open = !open" class="p-4 bg-gray-800 text-white font-bold text-lg rounded-t-lg cursor-pointer flex justify-between items-center">
                    <h2>Tabla de Posiciones</h2>
                    <span x-text="open ? '-' : '+'" class="text-xl"></span>
                </div>
                <div x-show="open" x-transition class="p-0">
                    @if(!empty($tablaPosiciones))
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
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
                                <tbody class="text-gray-700 text-sm font-light">
                                    @foreach($tablaPosiciones as $index => $team)
                                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-4 text-left font-semibold">{{ $index + 1 }}</td>
                                            <td class="py-3 px-4 text-left">{{ $team['nombre'] }}</td>
                                            <td class="py-3 px-4 text-center font-bold">{{ $team['Pts'] }}</td>
                                            <td class="py-3 px-4 text-center">{{ $team['PJ'] }}</td>
                                            <td class="py-3 px-4 text-center text-green-500">{{ $team['PG'] }}</td>
                                            <td class="py-3 px-4 text-center text-yellow-500">{{ $team['PE'] }}</td>
                                            <td class="py-3 px-4 text-center text-red-500">{{ $team['PP'] }}</td>
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
            <div x-data="{ open: true }" class="bg-white rounded-lg shadow-md">
                <div @click="open = !open" class="p-4 bg-gray-800 text-white font-bold text-lg rounded-t-lg cursor-pointer flex justify-between items-center">
                    <h2>Máximos Goleadores</h2>
                    <span x-text="open ? '-' : '+'" class="text-xl"></span>
                </div>
                <div x-show="open" x-transition class="p-0">
                    @if(!empty($goleadores))
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                        <th class="py-3 px-4 text-left">Jugador</th>
                                        <th class="py-3 px-4 text-left">Equipo</th>
                                        <th class="py-3 px-4 text-center">Goles</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm font-light">
                                    @foreach($goleadores as $goleador)
                                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-4 text-left">{{ $goleador->nombre }} {{ $goleador->apellido }}</td>
                                            <td class="py-3 px-4 text-left">{{ $goleador->equipo->nombre }}</td>
                                            <td class="py-3 px-4 text-center font-bold">{{ $goleador->goles }}</td>
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
</x-guest-layout>
