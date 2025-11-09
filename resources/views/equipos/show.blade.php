<x-app-layout>
    <!-- Team Header -->
    <header class="bg-copa-blue-900 py-6 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <form id="imageUploadForm" action="{{ route('equipos.updateImage', $equipo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="image_upload" class="cursor-pointer bg-white p-1 rounded-full">
                        <img src="{{ $equipo->imagen_url ?: 'http://static.photos/sport/60x60/' . ($equipo->id % 10) }}" alt="Escudo del equipo" 
                             class="w-12 h-12 md:w-16 md:h-16 rounded-full border-2 border-white hover:opacity-75 transition-opacity duration-300">
                    </label>
                    <input type="file" id="image_upload" name="imagen_equipo" class="hidden" onchange="document.getElementById('imageUploadForm').submit();">
                </form>
                <h1 class="text-white text-2xl md:text-3xl font-bold">{{ $equipo->nombre }}</h1>
                {{-- Edit Team Button --}}
                @can('update', $equipo) {{-- Assuming a policy for team update --}}
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-team-modal')" class="ml-4 p-2 rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors duration-200" title="Editar Equipo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                @endcan
            </div>
            <div class="hidden md:block">
                <span class="px-4 py-2 bg-white/20 rounded-full text-white font-medium">Temporada 2023/24</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('campeonatos.show', $equipo->campeonato_id) }}#participating-teams-section" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Regresar al Campeonato
            </a>
        </div>

        <!-- Players Grid Section -->
        <section class="mb-12" x-data="{ open: window.innerWidth >= 768, scrolling: false }" @resize.window="open = window.innerWidth >= 768">
            <div @touchstart="scrolling = false" @touchmove="scrolling = true" @click="if (!scrolling) open = !open" class="flex justify-between items-center cursor-pointer bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition mb-4">
                <h2 class="text-xl md:text-2xl font-bold text-copa-blue-900">Plantilla de Jugadores ({{ $jugadores->count() }})</h2>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-copa-blue-900 transition-transform duration-300" :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>

            <div x-show="open" x-collapse.duration.500ms>
                @if($canManage && $equipo->campeonato->registrations_open)
                <div class="mb-4">
                    <a href="{{ route('jugadores.create', ['equipo' => $equipo->id]) }}" class="inline-flex items-center px-4 py-2 bg-copa-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-copa-blue-900 active:bg-copa-blue-900 focus:outline-none focus:border-copa-blue-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Agregar Jugadores
                    </a>
                </div>
                @elseif($canManage && !$equipo->campeonato->registrations_open)
                <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                    <p class="font-semibold">Registros Cerrados</p>
                    <p class="text-sm">El período de traspasos y registros para este campeonato ha finalizado.</p>
                </div>
                @endif
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse($jugadores as $jugador)
                        @if($canManage && $equipo->campeonato->registrations_open)
                            <a href="{{ route('equipos.jugadores.edit', ['equipo' => $equipo->id, 'jugador' => $jugador->id]) }}" class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-200 hover:shadow-xl cursor-pointer block" data-aos="zoom-in">
                        @else
                            <div class="bg-white rounded-lg shadow-md overflow-hidden block" data-aos="zoom-in">
                        @endif
                                <div class="p-4 flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <span class="bg-copa-blue-700 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">
                                                {{ $jugador->numero_camiseta ?? '-' }}
                                            </span>
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $jugador->nombre }} {{ $jugador->apellido }}</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2">
                                            DNI: {{ $jugador->dni }}
                                        </p>
                                        <p class="text-gray-600 text-sm">
                                            Posición: {{ $jugador->posicion ?? 'N/A' }}
                                        </p>
                                        @if($jugador->fecha_nacimiento)
                                        <p class="text-gray-600 text-sm">
                                            Edad: {{ $jugador->edad }} años
                                        </p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end">
                                        @if($jugador->imagen_url)
                                            <img src="{{ $jugador->imagen_url }}" alt="{{ $jugador->nombre }} {{ $jugador->apellido }}" class="w-14 h-14 rounded-md object-cover">
                                        @else
                                            <div class="w-14 h-14 rounded-md bg-gray-200 flex items-center justify-center">
                                                <span class="text-xl font-bold text-gray-500">{{ $jugador->initials }}</span>
                                            </div>
                                        @endif
                                        @if(Auth::user()->role === 'admin' || Auth::user()->id === $equipo->campeonato->user_id)
                                            <form action="{{ route('equipos.jugadores.destroy', ['equipo' => $equipo->id, 'jugador' => $jugador->id]) }}" method="POST" class="mt-2" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este jugador? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Player Stats -->
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                    <div class="flex justify-between mb-2">
                                        <div class="flex items-center">
                                            <i data-feather="target" class="w-4 h-4 mr-1 text-copa-orange"></i>
                                            <span class="text-sm">Goles: {{ $jugador->goles }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-feather="alert-triangle" class="w-4 h-4 mr-1 text-copa-orange"></i>
                                            <span class="text-sm">Amarillas: {{ $jugador->tarjetas_amarillas }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-feather="x-octagon" class="w-4 h-4 mr-1 text-copa-red"></i>
                                            <span class="text-sm">Rojas: {{ $jugador->tarjetas_rojas }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Suspension Badge -->
                                    @if($jugador->suspendido)
                                    <div class="mt-2">
                                        <span class="bg-copa-red text-white px-2 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                            <i data-feather="alert-circle" class="w-3 h-3 mr-1"></i>
                                            Suspendido
                                        </span>
                                    </div>
                                    @endif
                                </div>
                        @if($canManage && $equipo->campeonato->registrations_open)
                            </a>
                        @else
                            </div>
                        @endif
                    @empty
                        <p class="text-gray-600 p-4">No hay jugadores registrados en este equipo todavía.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Stats & Matches Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Team Stats -->
            <section>
                <h2 class="text-xl font-bold text-copa-blue-900 mb-4" data-aos="fade-up">Alertas y Estadísticas del Equipo</h2>
                
                <div class="bg-white rounded-lg shadow p-6 space-y-6" data-aos="fade-up">
                    <!-- Jugadores Suspendidos -->
                    @if($jugadoresSuspendidos->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-semibold text-red-600 mb-3 flex items-center">
                                <i data-feather="alert-octagon" class="w-5 h-5 mr-2"></i> Jugadores Suspendidos
                            </h3>
                            <div class="space-y-2">
                                @foreach($jugadoresSuspendidos as $jugador)
                                    <div class="flex items-center justify-between p-2 bg-red-50 rounded-md">
                                        <div class="flex items-center">
                                            @if($jugador->imagen_url)
                                                <img src="{{ $jugador->imagen_url }}" alt="{{ $jugador->nombre }}" class="w-6 h-6 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                    <span class="text-xs font-bold text-gray-500">{{ $jugador->initials }}</span>
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-red-700">{{ $jugador->suspension_matches }} partido(s)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Jugadores con Tarjetas Rojas -->
                    @if($jugadoresConRojas->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-semibold text-red-500 mb-3 flex items-center">
                                <i data-feather="x-octagon" class="w-5 h-5 mr-2"></i> Jugadores con Tarjetas Rojas
                            </h3>
                            <div class="space-y-2">
                                @foreach($jugadoresConRojas as $jugador)
                                    <div class="flex items-center justify-between p-2 bg-red-50 rounded-md">
                                        <div class="flex items-center">
                                            @if($jugador->imagen_url)
                                                <img src="{{ $jugador->imagen_url }}" alt="{{ $jugador->nombre }}" class="w-6 h-6 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                    <span class="text-xs font-bold text-gray-500">{{ $jugador->initials }}</span>
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-red-700">{{ $jugador->tarjetas_rojas }} Roja(s)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Jugadores en Capilla -->
                    @if($jugadoresEnCapilla->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-600 mb-3 flex items-center">
                                <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Jugadores en Capilla
                            </h3>
                            <div class="space-y-2">
                                @foreach($jugadoresEnCapilla as $jugador)
                                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-md">
                                        <div class="flex items-center">
                                            @if($jugador->imagen_url)
                                                <img src="{{ $jugador->imagen_url }}" alt="{{ $jugador->nombre }}" class="w-6 h-6 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                    <span class="text-xs font-bold text-gray-500">{{ $jugador->initials }}</span>
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-yellow-700">{{ $jugador->tarjetas_amarillas }} Amarilla(s)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Máximos Goleadores del Equipo -->
                    @if($goleadores->isNotEmpty())
                        <div>
                            <h3 class="text-lg font-semibold text-green-600 mb-3 flex items-center">
                                <i data-feather="target" class="w-5 h-5 mr-2"></i> Máximos Goleadores
                            </h3>
                            <div class="space-y-2">
                                @foreach($goleadores as $jugador)
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-md">
                                        <div class="flex items-center">
                                            @if($jugador->imagen_url)
                                                <img src="{{ $jugador->imagen_url }}" alt="{{ $jugador->nombre }}" class="w-6 h-6 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                    <span class="text-xs font-bold text-gray-500">{{ $jugador->initials }}</span>
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">#{{ $jugador->goleador_rank }} {{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-green-700">{{ $jugador->goles }} Goles</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($jugadoresSuspendidos->isEmpty() && $jugadoresConRojas->isEmpty() && $jugadoresEnCapilla->isEmpty() && $goleadores->isEmpty())
                        <p class="text-gray-600">No hay alertas o estadísticas individuales destacadas para mostrar en este momento.</p>
                    @endif
                </div>
            </section>

            <!-- Last Matches -->
            <section class="mt-8">
                <h2 class="text-xl font-bold text-copa-blue-900 mb-4" data-aos="fade-up">Últimos Partidos</h2>
                
                <div class="space-y-4">
                    @forelse($ultimosPartidos as $partido)
                        @php
                            $esLocal = ($partido->equipo_local_id === $equipo->id);
                            $rival = $esLocal ? $partido->equipoVisitante : $partido->equipoLocal;
                            $resultadoEquipo = $esLocal ? $partido->goles_local : $partido->goles_visitante;
                            $resultadoRival = $esLocal ? $partido->goles_visitante : $partido->goles_local;

                            $claseBorde = 'border-gray-400'; // Default neutral
                            if ($partido->estado === 'finalizado') {
                                if ($resultadoEquipo > $resultadoRival) {
                                    $claseBorde = 'border-green-500'; // Victoria
                                } elseif ($resultadoEquipo < $resultadoRival) {
                                    $claseBorde = 'border-red-500'; // Derrota
                                } else {
                                    $claseBorde = 'border-copa-orange'; // Empate
                                }
                            }
                        @endphp
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $claseBorde }}" data-aos="fade-up">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold">vs {{ $rival->nombre }}</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-xl font-bold">
                                    {{ $resultadoEquipo }} - {{ $resultadoRival }}
                                </span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm">
                            @if($partido->estado === 'finalizado')
                                @if($resultadoEquipo > $resultadoRival)
                                <span class="text-green-600 font-medium">Victoria</span>
                                @elseif($resultadoEquipo < $resultadoRival)
                                <span class="text-copa-red font-medium">Derrota</span>
                                @else
                                <span class="text-copa-orange font-medium">Empate</span>
                                @endif
                            @else
                                <span class="text-gray-500 font-medium">{{ ucfirst($partido->estado) }}</span>
                            @endif
                        </p>
                    </div>
                    @empty
                        <p class="text-gray-600">No hay partidos recientes para mostrar.</p>
                    @endforelse
                </div>
            </section>
            
            <!-- Next Match -->
            <section class="mt-8">
                <h2 class="text-xl font-bold text-copa-blue-900 mb-4" data-aos="fade-up">Próximo Partido</h2>
                @if($proximoPartido)
                    @php
                        $esLocal = ($proximoPartido->equipo_local_id === $equipo->id);
                        $rival = $esLocal ? $proximoPartido->equipoVisitante : $proximoPartido->equipoLocal;
                    @endphp
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500" data-aos="fade-up">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold">vs {{ $rival->nombre }}</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($proximoPartido->fecha_partido)->format('d/m/Y H:i') }}</p>
                                <p class="text-gray-600 text-sm">Ubicación: {{ $proximoPartido->ubicacion_partido ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-blue-600 font-medium">Pendiente</span>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600">No hay un próximo partido programado para este equipo.</p>
                @endif
            </section>
        </div>
    </main>



    <!-- Footer -->
    <footer class="bg-copa-blue-900 text-gray-300 py-6">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm">© {{ date('Y') }} Copa Gol. Todos los derechos reservados.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i data-feather="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i data-feather="twitter" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i data-feather="instagram" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Feather Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple scroll animation implementation
            const aosElements = document.querySelectorAll('[data-aos]');
            
            function animateOnScroll() {
                aosElements.forEach(element => {
                    const rect = element.getBoundingClientRect();
                    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
                    
                    if (rect.top <= windowHeight * 0.85) {
                        element.classList.add('aos-animate');
                    }
                });
            }
            
            // Listen to scroll and resize events
            window.addEventListener('scroll', animateOnScroll);
            window.addEventListener('resize', animateOnScroll);
            animateOnScroll();
        });
    </script>

    <x-edit-team-form :equipo="$equipo" />
</x-app-layout>