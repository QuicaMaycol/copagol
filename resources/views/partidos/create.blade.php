<x-app-layout>
    {{-- Custom Header --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Nuevo Partido') }}
            </h2>
            <span class="text-sm font-medium text-gray-500">{{ $campeonato->nombre_campeonato }}</span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 bg-gray-800">
                    <form action="{{ route('partidos.store', $campeonato) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Equipo Local -->
                            <div>
                                <label for="equipo_local_id" class="block text-sm font-medium text-gray-300 mb-2">Equipo Local</label>
                                <select name="equipo_local_id" id="equipo_local_id" class="block w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="" class="text-gray-400">Seleccione un equipo</option>
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Equipo Visitante -->
                            <div>
                                <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-300 mb-2">Equipo Visitante</label>
                                <select name="equipo_visitante_id" id="equipo_visitante_id" class="block w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="" class="text-gray-400">Seleccione un equipo</option>
                                    {{-- Options will be populated by JS --}}
                                </select>
                            </div>

                            <!-- Fecha del Partido -->
                            <div>
                                <label for="fecha_partido" class="block text-sm font-medium text-gray-300 mb-2">Fecha del Partido (Opcional)</label>
                                <input type="datetime-local" name="fecha_partido" id="fecha_partido" class="block w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Jornada -->
                            <div>
                                <label for="jornada" class="block text-sm font-medium text-gray-300 mb-2">Jornada</label>
                                <input type="number" name="jornada" id="jornada" class="block w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required min="1" placeholder="Ej: 1">
                            </div>

                            <!-- Ubicacion -->
                            <div class="md:col-span-2">
                                <label for="ubicacion_partido" class="block text-sm font-medium text-gray-300 mb-2">Ubicación (Opcional)</label>
                                <select name="ubicacion_partido" id="ubicacion_partido" class="block w-full bg-gray-700 border border-gray-600 text-gray-200 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione una ubicación (si no se elige, se asignará automáticamente si es posible)</option>
                                    @if(isset($ubicaciones))
                                        @foreach($ubicaciones as $ubicacion)
                                            <option value="{{ $ubicacion }}">{{ $ubicacion }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-700">
                            <a href="{{ route('campeonatos.show', $campeonato) }}" class="text-gray-300 hover:bg-gray-700 font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Crear Partido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Style for the datetime-local icon */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campeonatoId = {{ $campeonato->id }};
            const equipoLocalSelect = document.getElementById('equipo_local_id');
            const equipoVisitanteSelect = document.getElementById('equipo_visitante_id');

            equipoLocalSelect.addEventListener('change', function () {
                const equipoLocalId = this.value;

                equipoVisitanteSelect.innerHTML = '<option value="">Cargando...</option>';
                equipoVisitanteSelect.disabled = true;

                if (!equipoLocalId) {
                    equipoVisitanteSelect.innerHTML = '<option value="">Seleccione un equipo</option>';
                    equipoVisitanteSelect.disabled = false;
                    return;
                }

                const url = `/campeonatos/${campeonatoId}/equipos/${equipoLocalId}/oponentes`;

                fetch(url)
                    .then(response => response.json())
                    .then(oponentes => {
                        equipoVisitanteSelect.innerHTML = '<option value="">Seleccione un equipo</option>';
                        
                        oponentes.forEach(oponente => {
                            const option = document.createElement('option');
                            option.value = oponente.id;
                            option.textContent = `${oponente.nombre} (${oponente.jugado ? 'Jugado' : 'Pendiente'})`;
                            option.disabled = oponente.jugado;
                            
                            if (oponente.jugado) {
                                option.style.color = '#f87171'; // Red-400
                            } else {
                                option.style.color = '#4ade80'; // Green-400
                            }
                            equipoVisitanteSelect.appendChild(option);
                        });

                        equipoVisitanteSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error al cargar los oponentes:', error);
                        equipoVisitanteSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            });

            if (equipoLocalSelect.value) {
                equipoLocalSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>