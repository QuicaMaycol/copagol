<x-app-layout>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen pb-12" x-data="shareComponent()">
        <!-- Tournament Header -->
        <header class="bg-[#2A3A5B] text-white py-6 px-4 sm:px-6 lg:px-8 shadow-md">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4">
                    <a href="{{ route('campeonatos.index') }}" class="text-white flex items-center hover:underline mb-4 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                        Volver a Campeonatos
                    </a>
                    
                    <div x-data="{ open: false }" class="relative">
                        <div class="hidden md:flex flex-row sm:flex-wrap items-stretch sm:items-center justify-end gap-2">
                            @can('share', $campeonato)
                            <button @click="share('{{ route('campeonatos.public.share', $campeonato) }}', '{{ $campeonato->nombre_torneo }}')" class="flex items-center justify-center bg-blue-500 hover:bg-blue-400 text-white px-3 py-2 rounded-md transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                                Compartir
                            </button>
                            @endcan
                            
                            @if($campeonato->reglamento_tipo)
                            <button @click="$dispatch('open-reglamento-modal')" class="flex items-center justify-center bg-purple-600 hover:bg-purple-500 text-white px-3 py-2 rounded-md transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20v2.5a2.5 2.5 0 0 1-2.5 2.5H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2H20"/><path d="M12 2a4 4 0 0 1 4 4v1.5a2.5 2.5 0 0 1 2.5 2.5v1.5a2.5 2.5 0 0 1-2.5 2.5H8.5A2.5 2.5 0 0 1 6 12.5V6a4 4 0 0 1 4-4z"/></svg>
                                Ver Reglamento
                            </button>
                            @endif
                            
                            @can('manage-campeonato', $campeonato)
                            <a href="{{ route('campeonatos.edit', $campeonato) }}" class="flex items-center justify-center bg-blue-500 hover:bg-blue-400 text-white px-3 py-2 rounded-md transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Editar Campeonato
                            </a>
                            @endcan
                            @can('update', $campeonato)
                            <form id="toggle-registrations-form" action="{{ route('campeonatos.toggle-registrations', $campeonato) }}" method="POST" class="w-full" x-data>
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-col items-center">
                                    <button type="submit" class="w-full flex items-center justify-center {{ $campeonato->registrations_open ? 'bg-red-500 hover:bg-red-400' : 'bg-green-500 hover:bg-green-400' }} text-white px-3 py-2 rounded-md transition-colors duration-300">
                                        @if($campeonato->registrations_open)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                        Cerrar Registros
                                        @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                        Abrir Registros
                                        @endif
                                    </button>
                                    <p class="text-center mt-2 text-base text-white">Este botón permite {{ $campeonato->registrations_open ? 'cerrar' : 'abrir' }} las inscripciones al campeonato.</p>
                                </div>
                            </form>
                            @endcan
                        </div>

                        <!-- Dropdown for small screens -->
                        <div class="md:hidden flex justify-end">
                            <button @click="open = !open" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-md flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                                <span class="ml-2">Opciones</span>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 right-0 mx-auto mt-2 w-full max-w-xs sm:max-w-sm md:w-48 bg-white rounded-md shadow-lg z-10">
                                @can('share', $campeonato)
                                <button @click="share('{{ route('campeonatos.public.share', $campeonato) }}', '{{ $campeonato->nombre_torneo }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Compartir
                                </button>
                                @endcan
                                
                                @if($campeonato->reglamento_tipo)
                                <button @click="$dispatch('open-reglamento-modal'); open = false;" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Ver Reglamento
                                </button>
                                @endif
                                
                                @can('manage-campeonato', $campeonato)
                                <a href="{{ route('campeonatos.edit', $campeonato) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Editar Campeonato
                                </a>
                                @endcan
                                @can('update', $campeonato)
                                <form id="toggle-registrations-form-mobile" action="{{ route('campeonatos.toggle-registrations', $campeonato) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        @if($campeonato->registrations_open)
                                        Cerrar Registros
                                        @else
                                        Abrir Registros
                                        @endif
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 mb-4">
                    <form id="imageUploadForm" action="{{ route('campeonatos.updateImage', $campeonato) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="image_upload" class="cursor-pointer">
                            <img src="{{ $campeonato->imagen_url ?: 'https://via.placeholder.com/96' }}" alt="Logo del Campeonato" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg hover:opacity-75 transition-opacity duration-300">
                        </label>
                        <input type="file" id="image_upload" name="imagen_campeonato" class="hidden" onchange="document.getElementById('imageUploadForm').submit();">
                    </form>
                    <div class="flex-grow">
                        <h1 class="text-3xl md:text-4xl font-bold">{{ $campeonato->nombre_campeonato }}</h1>
                        <p class="mt-2 text-blue-300">Organizado por {{ $campeonato->organizador->name }}</p>
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
                            $textoEstado = 'Inscripciones Abiertas';
                            $colorEstado = 'bg-yellow-500';
                        }
                    } elseif ($estado == 'en_curso') {
                        $textoEstado = 'En Curso';
                        $colorEstado = 'bg-green-500';
                    }
                    @endphp
                    <span class="{{ $colorEstado }} text-white text-sm font-semibold px-4 py-2 rounded-full">
                        {{ $textoEstado }}
                    </span>
                </div>
            </div>
        </header>
        @auth
            @php
                $userIsDelegate = Auth::user()->isDelegateOf($campeonato);
                $delegateTeam = Auth::user()->getTeamInCampeonato($campeonato);
                $registrationsOpen = $campeonato->registrations_open;
            @endphp

            @if($userIsDelegate)
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    @if($registrationsOpen && !$delegateTeam)
                        <a href="{{ route('equipos.create', ['campeonato' => $campeonato->id]) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Agregar mi equipo
                        </a>
                    @elseif($delegateTeam)
                        <a href="{{ route('equipos.show', $delegateTeam) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Mi Equipo: {{ $delegateTeam->nombre }}
                        </a>
                    @endif
                </div>
            @endif
        @endauth
        <!-- Main Content -->
        <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
            activeTab: 'partidos',
            showSancionadosModal: false,
            sancionados: [],
            conAmarilla: [],
            loading: false,
            getSancionados(partidoId) {
                this.loading = true;
                this.sancionados = [];
                this.conAmarilla = [];
                this.showSancionadosModal = true;
                fetch(`/partidos/${partidoId}/sancionados`)
                    .then(response => response.json())
                    .then(data => {
                        this.sancionados = data.sancionados;
                        this.conAmarilla = data.conAmarilla;
                        this.loading = false;
                    });
            },
            showDeleteModal: false,
            deleteUrl: '',
            partidoElementId: '',
            deletePartido() {
                fetch(this.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al eliminar el partido.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const element = document.getElementById(this.partidoElementId);
                        if (element) {
                            element.style.transition = 'opacity 0.5s ease';
                            element.style.opacity = '0';
                            setTimeout(() => element.remove(), 500);
                        }
                    }
                    this.showDeleteModal = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showDeleteModal = false;
                });
            }
        }">
            <!-- Tab Navigation -->
            <div class="border-b-2 border-gray-200 mb-6 flex overflow-x-auto space-x-6">
                <button @click="activeTab = 'tabla'" :class="{ 'border-[#3B82F6] text-[#3B82F6] font-bold': activeTab === 'tabla', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'tabla' }" class="whitespace-nowrap py-3 px-2 text-sm sm:px-4 sm:text-base border-b-2 transition-colors duration-300">
                    Tabla
                </button>
                <button @click="activeTab = 'partidos'" :class="{ 'border-[#3B82F6] text-[#3B82F6] font-bold': activeTab === 'partidos', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'partidos' }" class="whitespace-nowrap py-3 px-2 text-sm sm:px-4 sm:text-base border-b-2 transition-colors duration-300">
                    Partidos
                </button>
                <button @click="activeTab = 'goleadores'" :class="{ 'border-[#3B82F6] text-[#3B82F6] font-bold': activeTab === 'goleadores', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'goleadores' }" class="whitespace-nowrap py-3 px-2 text-sm sm:px-4 sm:text-base border-b-2 transition-colors duration-300">
                    Goleadores
                </button>
                <button @click="activeTab = 'fairplay'" :class="{ 'border-[#3B82F6] text-[#3B82F6] font-bold': activeTab === 'fairplay', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'fairplay' }" class="whitespace-nowrap py-3 px-2 text-sm sm:px-4 sm:text-base border-b-2 transition-colors duration-300">
                    Fair Play
                </button>
                <button @click="activeTab = 'sancionados'" :class="{ 'border-[#3B82F6] text-[#3B82F6] font-bold': activeTab === 'sancionados', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'sancionados' }" class="whitespace-nowrap py-3 px-2 text-sm sm:px-4 sm:text-base border-b-2 transition-colors duration-300">
                    Sancionados
                </button>
            </div>
            
            <!-- Tab Content -->
            <div class="space-y-8">
                <div x-show="activeTab === 'tabla'">
                    @if(isset($standings) && count($standings) > 0)
                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-blue-500 dark:bg-blue-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Equipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Pts</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PJ</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PG</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PE</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">GF</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">GC</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">DG</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($standings as $index => $equipo)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <a href="{{ route('equipos.show', $equipo['id']) }}" class="flex items-center group">
                                            <img class="h-8 w-8 rounded-full object-cover mr-3 border" src="{{ $equipo['imagen_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($equipo['nombre']) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo del equipo">
                                            <span class="font-semibold group-hover:text-blue-700 dark:group-hover:text-blue-400">{{ $equipo['nombre'] }}</span>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">{{ $equipo['Pts'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['PJ'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['PG'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['PE'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['PP'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['GF'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['GC'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['DG'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Tabla de Posiciones no Disponible</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">La tabla se mostrará una vez que el torneo haya comenzado y se hayan jugado partidos.</p>
                    </div>
                    @endif
                </div>
                
                <div x-show="activeTab === 'partidos'" x-data="{ subTab: 'proximos' }">
                    @can('manage-campeonato', $campeonato)
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Gestión de Calendario</h2>
                        <div class="flex flex-col sm:flex-row gap-4 mb-4">
                            <div x-data="{ showTooltip: false }" class="flex-grow relative">
                                <form action="{{ route('campeonatos.generate-calendar', $campeonato) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="tipo_torneo" class="sr-only">Tipo de Torneo</label>
                                        <select id="tipo_torneo" name="tipo_torneo" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                            <option value="una_sola_ronda">Una sola ronda</option>
                                            <option value="ida_vuelta">Ida y vuelta</option>
                                        </select>
                                    </div>
                                    <div
                                        @if($campeonato->partidos->count() > 0)
                                            @mouseover="showTooltip = true"
                                            @mouseleave="showTooltip = false"
                                        @endif
                                    >
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-lg flex items-center justify-center transition-colors duration-300 flex-shrink-0 w-full text-sm sm:text-base"
                                            {{ $campeonato->partidos->count() > 0 ? 'disabled' : '' }}
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            Generar Calendario
                                        </button>
                                    </div>
                                </form>
                                <template x-if="showTooltip">
                                    <div class="absolute z-10 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700" style="bottom: 100%; left: 50%; transform: translateX(-50%); white-space: nowrap;">
                                        Para generar un nuevo calendario, primero debes borrar todos los partidos existentes.
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </template>
                            </div>

                            <a href="{{ route('partidos.create', $campeonato) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-lg flex items-center transition-colors duration-300 w-full sm:w-auto justify-center text-sm sm:text-base self-end mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Agregar Partido
                            </a>
                            
                            @if($campeonato->partidos->count() > 0)
                            <form action="{{ route('campeonatos.reset-calendar', $campeonato) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres reiniciar el calendario? Todos los partidos y sus resultados se eliminarán permanentemente.');" class="w-full sm:w-auto self-end mb-4">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg flex items-center transition-colors duration-300 w-full sm:w-auto justify-center text-sm sm:text-base">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 14v-4m-7-7h4m14 0h-4M4.93 4.93l2.83 2.83m8.24 8.24l2.83 2.83M4.93 19.07l2.83-2.83m8.24-8.24l2.83-2.83"/></svg>
                                    Reiniciar Calendario
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endcan
                    
                    <div class="flex border-b border-gray-200 dark:border-gray-700">
                        <button @click="subTab = 'proximos'" :class="{ 'border-blue-700 text-blue-700 font-bold': subTab === 'proximos', 'border-transparent text-gray-500 hover:text-gray-700': subTab !== 'proximos' }" class="flex-1 whitespace-nowrap py-4 px-1 border-b-2 transition-colors duration-300">
                            Próximos
                        </button>
                        <button @click="subTab = 'resultados'" :class="{ 'border-blue-700 text-blue-700 font-bold': subTab === 'resultados', 'border-transparent text-gray-500 hover:text-gray-700': subTab !== 'resultados' }" class="flex-1 whitespace-nowrap py-4 px-1 border-b-2 transition-colors duration-300">
                            Resultados
                        </button>
                    </div>

                    <div class="py-6">
                        <div x-show="subTab === 'proximos'">
                            @if(isset($partidosProximos) && $partidosProximos->count() > 0)
                            <div class="space-y-8">
                                @foreach($partidosProximos as $jornada => $partidosEnJornada)
                                    @php
                                        $teamsInJornada = [];
                                        $duplicateTeamsInJornada = [];
                                        foreach ($partidosEnJornada as $partido) {
                                            if (in_array($partido->equipoLocal->id, $teamsInJornada)) {
                                                $duplicateTeamsInJornada[] = $partido->equipoLocal->id;
                                            } else {
                                                $teamsInJornada[] = $partido->equipoLocal->id;
                                            }

                                            if (in_array($partido->equipoVisitante->id, $teamsInJornada)) {
                                                $duplicateTeamsInJornada[] = $partido->equipoVisitante->id;
                                            } else {
                                                $teamsInJornada[] = $partido->equipoVisitante->id;
                                            }
                                        }
                                        $duplicateTeamsInJornada = array_unique($duplicateTeamsInJornada);
                                    @endphp
                                <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md" x-data="{ open: false }">
                                    <div class="flex justify-between items-center mb-4 border-b pb-2 cursor-pointer" @click="open = !open">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Jornada {{ $jornada }}</h3>
                                        @if(isset($restingTeamsByJornada[$jornada]))
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Equipo que descansa: <span class="font-semibold">{{ $restingTeamsByJornada[$jornada] }}</span></p>
                                        @endif
                                        <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="space-y-4" x-show="open" x-collapse>
                                        @foreach($partidosEnJornada as $partido)
                                        <div id="partido-{{ $partido->id }}" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg shadow-sm border-l-4 {{ in_array($partido->id, $duplicateMatchIds ?? []) ? 'border-red-500' : 'border-blue-500' }}">
                                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                                <div class="w-full flex-grow flex flex-col sm:flex-row items-center justify-center gap-2">
                                                    <!-- Local Team -->
                                                    <div class="sm:flex-1 flex items-center justify-center sm:justify-end space-x-3">
                                                        <span class="font-semibold text-gray-800 dark:text-gray-100 {{ in_array($partido->equipoLocal->id, $duplicateTeamsInJornada) ? 'text-red-500' : '' }} text-right">{{ $partido->equipoLocal->nombre }}</span>
                                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $partido->equipoLocal->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoLocal->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo Equipo Local">
                                                    </div>
                                                    
                                                    <!-- Center Info -->
                                                    <div class="text-center flex-shrink-0 px-4">
                                                        <div class="font-bold text-gray-500">vs</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</div>
                                                    </div>
                                        
                                                    <!-- Visitor Team -->
                                                    <div class="sm:flex-1 flex items-center justify-center sm:justify-start space-x-3">
                                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $partido->equipoVisitante->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoVisitante->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo Equipo Visitante">
                                                        <span class="font-semibold text-gray-800 dark:text-gray-100 {{ in_array($partido->equipoVisitante->id, $duplicateTeamsInJornada) ? 'text-red-500' : '' }} text-left">{{ $partido->equipoVisitante->nombre }}</span>
                                                    </div>
                                                </div>
                                        
                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2 flex-shrink-0">
                                                    @if($partido->estado === 'suspendido')
                                                        <span class="inline-block bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full">Suspendido</span>
                                                    @endif
                                                    <button @click="getSancionados({{ $partido->id }})" class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-full transition-colors duration-300" title="Ver Sancionados">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                                    </button>
                                                    @can('manage-campeonato', $campeonato)
                                                    <a href="{{ route('partidos.edit', $partido) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full transition-colors duration-300" title="Editar Partido">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    </a>
                                                    <button @click="showDeleteModal = true; deleteUrl = '{{ route('partidos.destroy', $partido) }}'; partidoElementId = 'partido-{{ $partido->id }}';" type="button" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-300" title="Eliminar Partido">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                            @if(in_array($partido->id, $duplicateMatchIds ?? []))
                                                <p class="text-red-500 text-xs mt-2 font-bold text-center">Aviso: Este enfrentamiento ya se ha programado en otra jornada.</p>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay partidos próximos</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Genera el calendario para ver los próximos partidos.</p>
                            </div>
                            @endif
                        </div>
                        
                        <div x-show="subTab === 'resultados'" style="display: none;">
                            @if(isset($partidosJugados) && count($partidosJugados) > 0)
                            <div class="space-y-4">
                                @foreach($partidosJugados as $jornada => $partidosEnJornada)
                                <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md" x-data="{ open: true }">
                                    <div class="flex justify-between items-center mb-4 border-b pb-2 cursor-pointer" @click="open = !open">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Jornada {{ $jornada }}</h3>
                                        @if(isset($restingTeamsByJornada[$jornada]))
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Equipo que descansa: <span class="font-semibold">{{ $restingTeamsByJornada[$jornada] }}</span></p>
                                        @endif
                                        <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="space-y-4" x-show="open" x-collapse>
                                        @foreach($partidosEnJornada as $partido)
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border-l-4 {{ $partido->goles_local > $partido->goles_visitante ? 'border-green-500' : ($partido->goles_local < $partido->goles_visitante ? 'border-red-500' : 'border-yellow-500') }}">
                                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                                <!-- Local Team -->
                                                <div class="flex items-center space-x-3 w-full sm:w-auto justify-start flex-1">
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $partido->equipoLocal->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoLocal->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $partido->equipoLocal->nombre }}</span>
                                                </div>
                                                <!-- Score -->
                                                <div class="text-center flex-shrink-0">
                                                    <span class="text-xl font-bold">{{ $partido->goles_local }} - {{ $partido->goles_visitante }}</span>
                                                    <p class="text-xs text-gray-400">Finalizado</p>
                                                    @can('manage-campeonato', $campeonato)
                                                    <div class="mt-2">
                                                        <a href="{{ route('partidos.edit', $partido) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full transition-colors duration-300 inline-block">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        </a>
                                                    </div>
                                                    @endcan
                                                </div>
                                                <!-- Visitor Team -->
                                                <div class="flex items-center space-x-3 w-full sm:w-auto justify-end flex-1">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $partido->equipoVisitante->nombre }}</span>
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $partido->equipoVisitante->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($partido->equipoVisitante->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay resultados de partidos</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los resultados se mostrarán aquí una vez que se hayan jugado los partidos.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div x-show="activeTab === 'goleadores'">
                    @if(isset($goleadores) && count($goleadores) > 0)
                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-blue-500 dark:bg-blue-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Jugador</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Equipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Goles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($goleadores as $index => $jugador)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full object-cover mr-3 border" src="{{ $jugador->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($jugador->nombre . ' ' . $jugador->apellido) . '&color=7F9CF5&background=EBF4FF' }}" alt="Foto de {{ $jugador->nombre }}">
                                            <span class="font-semibold">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <a href="{{ route('equipos.show', $jugador->equipo) }}" class="flex items-center group">
                                            <img class="h-8 w-8 rounded-full object-cover mr-3 border" src="{{ $jugador->equipo->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($jugador->equipo->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo del equipo">
                                            <span class="font-semibold group-hover:text-blue-700 dark:group-hover:text-blue-400">{{ $jugador->equipo->nombre }}</span>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">{{ $jugador->goles }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay goleadores registrados</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los goleadores se mostrarán aquí una vez que se hayan registrado goles en los partidos.</p>
                    </div>
                    @endif
                </div>
                <div x-show="activeTab === 'fairplay'">
                    @if(isset($fairPlay) && count($fairPlay) > 0)
                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-blue-500 dark:bg-blue-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Equipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Amarillas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Rojas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Puntos Fair Play</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($fairPlay as $index => $equipo)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span class="font-semibold">{{ $equipo['nombre'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['amarillas'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $equipo['rojas'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">{{ $equipo['puntos'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay datos de Fair Play registrados</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los datos de Fair Play se mostrarán aquí una vez que se hayan registrado tarjetas en los partidos.</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Other Sections -->
            <section id="progress-section" class="bg-white dark:bg-gray-800 py-8 px-4 sm:px-6 lg:px-8 mt-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Progreso del Torneo</h2>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-6 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
                        <div style="width:{{ $progressPercentage }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $progressPercentage > 50 ? 'bg-green-500' : 'bg-orange-500' }}"></div>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-2">
                    @if($fechaInicio)
                    <span>{{ \Carbon\Carbon::parse($fechaInicio)->format('d M Y') }}</span>
                    @else
                    <span>Inicio</span>
                    @endif
                    @if($fechaFin)
                    <span>{{ \Carbon\Carbon::parse($fechaFin)->format('d M Y') }}</span>
                    @else
                    <span>Fin</span>
                    @endif
                </div>
                @if($totalPartidos > 0)
                <p class="mt-4 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $partidosFinalizados }} de {{ $totalPartidos }} partidos finalizados</p>
                @else
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Aún no hay partidos para mostrar el progreso.</p>
                @endif
            </section>
            
            <section id="participating-teams-section" class="bg-white dark:bg-gray-800 py-8 px-4 sm:px-6 lg:px-8 mt-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Equipos Participantes ({{ $campeonato->equipos->count() }} / {{ $campeonato->equipos_max }})</h2>
                @if($campeonato->equipos->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($campeonato->equipos as $equipo)
                    <a href="{{ route('equipos.show', $equipo) }}" class="block group text-center">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-4 transition-all duration-300 ease-in-out group-hover:shadow-xl group-hover:transform group-hover:-translate-y-1 h-full flex flex-col items-center justify-center">
                            <img class="h-20 w-20 rounded-full object-cover mx-auto mb-3 border-2 border-gray-200 dark:border-gray-700 group-hover:border-blue-500" src="{{ $equipo->imagen_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($equipo->nombre) . '&color=7F9CF5&background=EBF4FF' }}" alt="Logo de {{ $equipo->nombre }}">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-blue-700 dark:group-hover:text-blue-400 leading-tight">{{ $equipo->nombre }}</h3>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay equipos inscritos todavía</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los equipos que se unan al campeonato aparecerán aquí.</p>
                </div>
                @endif
            </section>
            
            @can('manage-campeonato', $campeonato)
            <section id="delegates-section" class="bg-white dark:bg-gray-800 py-8 px-4 sm:px-6 lg:px-8 mt-8 rounded-lg shadow-md" x-data="{ openDelegates: false }">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center mb-4 cursor-pointer" @click="openDelegates = !openDelegates">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Delegados</h2>
                        <div class="flex items-center space-x-2">
                                                    <button @click.stop="$dispatch('open-add-delegate-team-modal')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg font-semibold flex items-center transition-colors duration-300 text-sm sm:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Agregar Equipo
                        </button>
                            <svg :class="{'rotate-180': openDelegates}" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <div x-show="openDelegates" x-collapse>
                        @if($campeonato->delegates->count() > 0)
                        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DNI</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($campeonato->delegates as $delegate)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $delegate->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $delegate->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $delegate->dni }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('campeonatos.delegates.destroy', ['campeonato' => $campeonato, 'user' => $delegate]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este delegado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-300">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V4m0 16l-4-4m4 4l4-4M6 20h12"/></svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No hay delegados asignados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Añade delegados para que puedan gestionar sus equipos.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </section>
            @endcan

            <!-- News Section -->
            <section id="news-section" class="bg-gray-50 dark:bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
                <div class="container mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Noticias y Avisos</h2>
                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Próxima Reunión de Delegados</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Se informa que la próxima reunión de delegados para discutir las fases eliminatorias se llevará a cabo el próximo sábado a las 18:00 hrs. ¡No faltes!</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">¡Inscripciones a punto de cerrar!</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">¡Últimos 2 cupos para el torneo! Si aún no has inscrito a tu equipo, esta es tu oportunidad. No te quedes fuera de la Copa Gol.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sancionados Modal -->
            <div
                x-show="showSancionadosModal"
                x-on:keydown.escape.window="showSancionadosModal = false"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
                style="display: none;"
            >
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div x-show="showSancionadosModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                         @click="showSancionadosModal = false"
                         aria-hidden="true"></div>

                    <!-- Modal panel -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showSancionadosModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">

                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                            Jugadores en Riesgo
                        </h3>

                        <div class="mt-4">
                            <div x-show="loading" class="text-center py-8">
                                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 mt-2">Cargando...</p>
                            </div>

                            <div x-show="!loading">
                                <!-- Suspendidos -->
                                <h4 class="text-lg font-semibold text-red-600 dark:text-red-500 mt-4 mb-2">Suspendidos para este partido</h4>
                                <div x-show="sancionados.length > 0">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="jugador in sancionados" :key="jugador.nombre">
                                            <li class="py-2 flex justify-between items-center">
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-gray-200" x-text="jugador.nombre"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="jugador.equipo"></p>
                                                </div>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" x-text="jugador.tipo_sancion"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div x-show="sancionados.length === 0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay jugadores suspendidos para este partido.</p>
                                </div>

                                <!-- Con Amarilla -->
                                <h4 class="text-lg font-semibold text-yellow-600 dark:text-yellow-500 mt-6 mb-2">Apercibidos (con Tarjeta Amarilla)</h4>
                                <div x-show="conAmarilla.length > 0">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="jugador in conAmarilla" :key="jugador.nombre">
                                            <li class="py-2 flex justify-between items-center">
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-gray-200" x-text="jugador.nombre"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="jugador.equipo"></p>
                                                </div>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" x-text="jugador.cantidad + ' Amarilla(s)'"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                 <div x-show="conAmarilla.length === 0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay jugadores con tarjetas amarillas en los equipos de este partido.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button @click="showSancionadosModal = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal" x-on:keydown.escape.window="showDeleteModal = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showDeleteModal = false" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title-delete">
                            Eliminar Partido
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ¿Estás seguro de que quieres eliminar este partido? Esta acción no se puede deshacer.
                            </p>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button @click="showDeleteModal = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-500">
                                Cancelar
                            </button>
                            <button @click="deletePartido()" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-500">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
    <x-add-delegate-and-team-modal :campeonato="$campeonato" />
    <x-campeonato-reglamento-modal :campeonato="$campeonato" />
    <x-share-modal />

    <script>
        function shareComponent() {
          return {
            share(shareUrl, title) {
              if (navigator.share) {
                navigator.share({
                  title: title,
                  text: `Échale un vistazo al campeonato: ${title}`,
                  url: shareUrl
                }).catch(console.error);
              } else {
                this.$dispatch('open-share-modal', { url: shareUrl });
              }
            }
          }
        }
    </script>
</x-app-layout>