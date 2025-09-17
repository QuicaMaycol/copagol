<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Fase: {{ $fase->nombre }} (Campeonato: {{ $campeonato->nombre_torneo }})</h1>

                    <div class="mb-6 text-gray-700">
                        <p class="mb-1"><strong>Tipo:</strong> <span class="font-medium">{{ $fase->tipo }}</span></p>
                        <p class="mb-1"><strong>Estado:</strong> <span class="font-medium">{{ $fase->estado }}</span></p>
                        <p><strong>Orden:</strong> <span class="font-medium">{{ $fase->orden }}</span></p>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Partidos de esta Fase</h2>
                        <div class="flex space-x-2">
                            @can('manage-campeonato', $campeonato)
                                <a href="{{ route('campeonatos.fases.partidos.create', [$campeonato, $fase]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-copa-blue-700 hover:bg-copa-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copa-blue-500">
                                    Crear Partido
                                </a>
                                <button type="button" @click="$dispatch('open-suspended-players-modal', { campeonatoId: {{ $campeonato->id }}, faseId: {{ $fase->id }} })" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Ver Jugadores Sancionados
                                </button>
                                <form action="{{ route('fases.destroy', [$campeonato, $fase]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta fase y todos sus partidos? Esta acción es irreversible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Eliminar Fase
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    @if($fase->partidos->isEmpty())
                        <p class="text-gray-500">No hay partidos creados para esta fase aún.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md">
                                <thead class="bg-copa-blue-700 text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Equipo Local</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Goles Local</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Goles Visitante</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Equipo Visitante</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Fecha</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Ubicación</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Estado</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($fase->partidos as $partido)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $partido->equipoLocal->nombre }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">{{ $partido->goles_local }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">{{ $partido->goles_visitante }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $partido->equipoVisitante->nombre }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $partido->ubicacion_partido }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $partido->estado }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @can('manage-campeonato', $campeonato)
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('partidos.edit', $partido) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Editar</a>
                                                        <button type="button" @click="$dispatch('open-match-result-modal', { match: {{ $partido->toJson() }} })" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">Cargar/Editar Resultado</button>
                                                        <form action="{{ route('partidos.destroy', $partido) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este partido?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Eliminar</button>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('campeonatos.show', $campeonato) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Volver al Campeonato
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.match-result-modal')
</x-app-layout>
