<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Crear Partido para Fase: {{ $fase->nombre }} (Campeonato: {{ $campeonato->nombre_torneo }})</h1>

                    <form action="{{ route('campeonatos.fases.partidos.store', [$campeonato, $fase]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="equipo_local_id" class="block text-sm font-medium text-gray-700">Equipo Local</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" id="equipo_local_id" name="equipo_local_id" required>
                                <option value="">Seleccione Equipo Local</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-700">Equipo Visitante</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" id="equipo_visitante_id" name="equipo_visitante_id" required>
                                <option value="">Seleccione Equipo Visitante</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="fecha_partido" class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                            <input type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="fecha_partido" name="fecha_partido" required>
                        </div>
                        <div>
                            <label for="ubicacion_partido" class="block text-sm font-medium text-gray-700">Cancha</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="ubicacion_partido" name="ubicacion_partido" placeholder="Ej: Cancha Principal, Estadio Municipal">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-copa-blue-700 hover:bg-copa-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copa-blue-500">
                            Crear Partido
                        </button>
                        <a href="{{ route('campeonatos.fases.show', [$campeonato, $fase]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-4">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
