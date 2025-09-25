<!-- resources/views/campeonatos/partials/_jornada_accordion.blade.php -->
<div class="mb-4">
    {{-- Cabecera de la Jornada (Clickable) --}}
    <div @click="openJornada = openJornada === {{ $jornada }} ? null : {{ $jornada }}" 
         class="p-4 font-bold bg-gray-800 text-gray-200 cursor-pointer flex justify-between items-center transition-colors duration-200 hover:bg-gray-700 rounded-lg">
        <span>Jornada {{ $jornada }}</span>
        <svg :class="{'rotate-180': openJornada === {{ $jornada }} }" class="w-5 h-5 text-gray-400 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </div>

    {{-- Contenido de la Jornada (Colapsable) --}}
    <div x-show="openJornada === {{ $jornada }}" x-transition class="p-2 sm:p-4 bg-gray-800/50">

        @if(isset($restingTeamsByJornada[$jornada]))
            <div class="bg-gray-700 text-gray-300 p-3 rounded-lg mb-4 text-sm text-center font-semibold border border-gray-600">
                Descansa: {{ $restingTeamsByJornada[$jornada] }}
            </div>
        @endif

        <div class="space-y-3">
            @foreach($matches->sortBy('fecha_partido') as $match)
                <a href="{{ route('partidos.public_show', ['partido' => $match->id]) }}" class="block p-4 bg-gray-900 hover:bg-gray-700/70 rounded-lg transition-colors duration-200">
                    <div class="flex justify-between items-center text-xs text-gray-400 mb-2">
                        <span>{{ \Carbon\Carbon::parse($match->fecha_partido)->translatedFormat('D d M') }}</span>
                        <span>{{ $match->ubicacion_partido ?? '' }}</span>
                    </div>
                    <div class="flex items-center text-lg">
                        <div class="flex-1 text-right font-semibold text-gray-100">{{ $match->equipoLocal->nombre }}</div>
                        <div class="w-28 text-center mx-2">
                            @if($isResultados)
                                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-md font-extrabold text-xl">
                                    {{ $match->goles_local }} - {{ $match->goles_visitante }}
                                </span>
                            @else
                                <div class="font-bold text-gray-300 text-xl">
                                    {{ \Carbon\Carbon::parse($match->fecha_partido)->format('H:i') }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 text-left font-semibold text-gray-100">{{ $match->equipoVisitante->nombre }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>