<x-app-layout>
    {{-- Custom Header for Tournaments --}}
    <header class="bg-azul text-white py-4 px-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Campeonatos</h1>
            @unless(Auth::user()->role === 'delegado')
            <button id="open-create-modal-btn" class="bg-naranja hover:bg-opacity-90 text-white px-4 py-2 rounded-lg font-medium flex items-center transition">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Crear nuevo campeonato
            </button>
            @endunless
        </div>
    </header>

    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-8">
        {{-- Success Messages --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Search Form -->
        <div class="mb-8">
            <form action="{{ route('campeonatos.index') }}" method="GET">
                <div class="flex rounded-lg shadow-sm">
                    <input type="text" name="search" placeholder="Buscar por nombre de torneo..." class="w-full px-4 py-2 border-t border-b border-l border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-azul" value="{{ request('search') }}">
                    <button type="submit" class="bg-azul text-white px-6 py-2 rounded-r-md hover:bg-opacity-90 transition">Buscar</button>
                </div>
            </form>
        </div>

        {{-- Championships List --}}
        @if($campeonatos->isEmpty())
            <div class="text-center text-gray-500 py-12">
                @if(request()->filled('search'))
                    <p class="text-lg">No se encontraron campeonatos que coincidan con tu búsqueda.</p>
                @else
                    <p class="text-lg">No hay campeonatos registrados por el momento.</p>
                @endif
            </div>
        @else
            <!-- Championships Grid -->
            <div id="campeonatos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($campeonatos as $campeonato)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="p-5 border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $campeonato->imagen_url ?: 'http://static.photos/sport/60x60/' . ($campeonato->id % 10) }}" alt="Organizador" class="w-12 h-12 rounded-full mr-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900">{{ $campeonato->nombre_torneo }}</h3>
                                        <p class="text-sm text-gray-600">Organizado por {{ $campeonato->organizador->name }}</p>
                                    </div>
                                </div>
                                @php
                                    $equiposInscritos = $campeonato->equipos->count();
                                    $maxEquipos = $campeonato->equipos_max;
                                    $estado = $campeonato->estado_torneo;
                                    $textoEstado = 'Finalizado';
                                    $colorEstado = 'bg-gray-500';

                                    if ($estado == 'inscripciones_abiertas') {
                                        if ($equiposInscritos >= $maxEquipos) {
                                            $textoEstado = 'En Curso';
                                            $colorEstado = 'bg-green-500';
                                        } else {
                                            $textoEstado = 'Inscripciones';
                                            $colorEstado = 'bg-yellow-500';
                                        }
                                    } elseif ($estado == 'en_curso') {
                                        $textoEstado = 'En Curso';
                                        $colorEstado = 'bg-green-500';
                                    }
                                @endphp
                                <span class="{{ $colorEstado }} text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $textoEstado }}</span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <i data-feather="map-pin" class="w-4 h-4 mr-1"></i>
                                <span>
                                @if($campeonato->ubicacion_tipo == 'unica')
                                    {{ $campeonato->cancha_unica_direccion }}
                                @else
                                    Sedes de equipos locales
                                @endif
                            </span>
                            </div>
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Fútbol {{ $campeonato->tipo_futbol }}</span>
                                <span class="bg-azul text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ ucfirst($campeonato->privacidad) }}</span>
                            </div>
                            
                            <div class="mb-4">
                                @php
                                    $equiposInscritos = $campeonato->equipos->count();
                                    $maxEquipos = $campeonato->equipos_max > 0 ? $campeonato->equipos_max : 1;
                                    $participationPercentage = round(($equiposInscritos / $maxEquipos) * 100);
                                @endphp
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Participación</span>
                                    <span class="font-medium">{{ $equiposInscritos }}/{{ $campeonato->equipos_max ?? '?' }} equipos</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-azul h-2 rounded-full" style="width: {{ $participationPercentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2 mt-4">
                                <a href="{{ route('campeonatos.show', $campeonato) }}" class="bg-azul hover:bg-opacity-90 text-white px-3 py-1.5 rounded text-sm flex items-center flex-1 justify-center transition">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i>
                                    Ver detalles
                                </a>
                                @can('update', $campeonato)
                                <button type="button" class="open-edit-modal-btn bg-blue-500 hover:bg-blue-600 text-white p-2 rounded text-sm flex items-center transition" data-campeonato='{{ json_encode($campeonato) }}' data-update-url="{{ route('campeonatos.update', $campeonato) }}">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                @endcan
                                @can('delete', $campeonato)
                                <form action="{{ route('campeonatos.destroy', $campeonato) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este campeonato?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rojo hover:bg-opacity-90 text-white p-2 rounded text-sm flex items-center transition">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            @if ($campeonatos->hasPages())
                <div class="mt-8">
                    {{ $campeonatos->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </main>

    @include('components.campeonato-modal')
</x-app-layout>