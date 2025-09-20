<x-guest-layout>
    <div class="container mx-auto p-4 bg-gray-50 font-sans">

        <!-- Encabezado y Volver -->
        <div class="relative text-center mb-8">
            <a href="{{ route('campeonatos.public.share', ['campeonato' => $partido->campeonato->id]) }}" class="absolute left-0 top-0 text-blue-500 hover:text-blue-700 font-semibold flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Volver
            </a>
            <h1 class="text-3xl font-bold text-gray-800">{{ $partido->campeonato->nombre_campeonato }}</h1>
            <p class="text-md text-gray-600">Jornada {{ $partido->jornada }}</p>
        </div>

        <!-- Marcador Principal -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 max-w-3xl mx-auto">
            <div class="flex items-center justify-around text-2xl md:text-3xl font-bold">
                <div class="flex-1 text-center">
                    <img src="{{ $partido->equipoLocal->imagen_url ?? 'http://static.photos/sport/100x100/' . $partido->equipoLocal->id }}" alt="{{ $partido->equipoLocal->nombre }}" class="w-20 h-20 mx-auto mb-2 rounded-full object-cover">
                    <h2 class="text-gray-800">{{ $partido->equipoLocal->nombre }}</h2>
                </div>
                <div class="w-32 text-center font-extrabold text-4xl md:text-5xl mx-4">
                    @if($partido->estado === 'finalizado')
                        <span class="text-gray-800">{{ $partido->goles_local }} - {{ $partido->goles_visitante }}</span>
                    @else
                        <span class="text-gray-400">vs</span>
                    @endif
                </div>
                <div class="flex-1 text-center">
                    <img src="{{ $partido->equipoVisitante->imagen_url ?? 'http://static.photos/sport/100x100/' . $partido->equipoVisitante->id }}" alt="{{ $partido->equipoVisitante->nombre }}" class="w-20 h-20 mx-auto mb-2 rounded-full object-cover">
                    <h2 class="text-gray-800">{{ $partido->equipoVisitante->nombre }}</h2>
                </div>
            </div>
            <div class="text-center text-sm text-gray-500 mt-4">
                <span>{{ \Carbon\Carbon::parse($partido->fecha_partido)->translatedFormat('l, d F Y, H:i') }}</span>
                <span class="mx-2">|</span>
                <span>{{ $partido->ubicacion_partido ?? 'Sin Ubicación' }}</span>
            </div>
        </div>

        <!-- Alineaciones -->
        <h3 class="text-2xl font-bold text-center text-gray-700 mb-6">Alineaciones</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            
            <!-- Equipo Local -->
            <div>
                <h4 class="font-bold text-xl mb-4 text-gray-800">{{ $partido->equipoLocal->nombre }}</h4>
                <div class="space-y-2">
                    @forelse($partido->equipoLocal->jugadores->sortBy('numero_camiseta') as $jugador)
                        <div class="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-gray-500 font-bold w-8 text-center">{{ $jugador->numero_camiseta ?? '-' }}</span>
                                <span class="ml-3 font-semibold text-gray-700">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($jugador->goles > 0)
                                    <span class="text-xs font-bold text-green-600">⚽ {{ $jugador->goles }}</span>
                                @endif
                                @if($jugador->tarjetas_amarillas > 0)
                                    <span class="w-4 h-5 bg-yellow-400 text-black text-xs flex items-center justify-center font-bold rounded-sm">{{ $jugador->tarjetas_amarillas }}</span>
                                @endif
                                @if($jugador->tarjetas_rojas > 0)
                                    <span class="w-4 h-5 bg-red-600 text-white text-xs flex items-center justify-center font-bold rounded-sm">{{ $jugador->tarjetas_rojas }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No hay jugadores registrados para este equipo.</p>
                    @endforelse
                </div>
            </div>

            <!-- Equipo Visitante -->
            <div>
                <h4 class="font-bold text-xl mb-4 text-gray-800">{{ $partido->equipoVisitante->nombre }}</h4>
                <div class="space-y-2">
                    @forelse($partido->equipoVisitante->jugadores->sortBy('numero_camiseta') as $jugador)
                        <div class="bg-white p-3 rounded-lg shadow-sm flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-gray-500 font-bold w-8 text-center">{{ $jugador->numero_camiseta ?? '-' }}</span>
                                <span class="ml-3 font-semibold text-gray-700">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($jugador->goles > 0)
                                    <span class="text-xs font-bold text-green-600">⚽ {{ $jugador->goles }}</span>
                                @endif
                                @if($jugador->tarjetas_amarillas > 0)
                                    <span class="w-4 h-5 bg-yellow-400 text-black text-xs flex items-center justify-center font-bold rounded-sm">{{ $jugador->tarjetas_amarillas }}</span>
                                @endif
                                @if($jugador->tarjetas_rojas > 0)
                                    <span class="w-4 h-5 bg-red-600 text-white text-xs flex items-center justify-center font-bold rounded-sm">{{ $jugador->tarjetas_rojas }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No hay jugadores registrados para este equipo.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
