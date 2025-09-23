<!-- resources/views/campeonatos/partials/_jornada_accordion.blade.php -->
<div class="mb-2 bg-white rounded-lg shadow-md">
    {{-- Cabecera de la Jornada (Clickable) --}}
    <div @click="openJornada = openJornada === {{ $jornada }} ? null : {{ $jornada }}" 
         class="p-4 font-bold text-gray-700 cursor-pointer flex justify-between items-center transition-colors duration-200 hover:bg-gray-100">
        <span>Jornada {{ $jornada }}</span>
        <svg :class="{'rotate-180': openJornada === {{ $jornada }} }" class="w-5 h-5 text-gray-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </div>

    {{-- Contenido de la Jornada (Colapsable) --}}
    <div x-show="openJornada === {{ $jornada }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="border-t border-gray-200 p-4">
        
        @if(isset($restingTeamsByJornada[$jornada]))
            <div class="bg-blue-50 text-blue-700 p-3 rounded-lg mb-4 text-sm text-center font-semibold border border-blue-200">
                Descansa: {{ $restingTeamsByJornada[$jornada] }}
            </div>
        @endif

        @foreach($matches->sortBy('fecha_partido') as $match)
            <a href="{{ route('partidos.public_show', ['partido' => $match->id]) }}" class="block mb-2 last:mb-0 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                <div class="flex items-center justify-between text-sm text-gray-500 mb-1">
                    <span>{{ \Carbon\Carbon::parse($match->fecha_partido)->translatedFormat('D d M') }}</span>
                    <span>{{ $match->ubicacion_partido ?? '' }}</span>
                </div>
                <div class="flex items-center text-lg">
                    <div class="flex-1 text-right font-bold text-gray-800">{{ $match->equipoLocal->nombre }}</div>
                    <div class="w-24 text-center mx-2">
                        @if($isResultados)
                            <span class="bg-gray-800 text-white px-3 py-1 rounded-md font-extrabold text-xl">{{ $match->goles_local }} - {{ $match->goles_visitante }}</span>
                        @else
                            <div class="font-bold text-gray-500">vs</div>
                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($match->fecha_partido)->format('H:i') }}</div>
                        @endif
                    </div>
                    <div class="flex-1 text-left font-bold text-gray-800">{{ $match->equipoVisitante->nombre }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
