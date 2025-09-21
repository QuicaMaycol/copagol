<x-guest-layout>
    <div class="bg-gray-100 font-sans">
        <div class="container mx-auto p-4">
            <!-- Header del Equipo -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center">
                    <img src="{{ $equipo->imagen_url ?? asset('img/logo.png') }}" alt="Logo del Equipo" class="w-24 h-24 rounded-full object-cover mr-6">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-800">{{ $equipo->nombre }}</h1>
                        <p class="text-lg text-gray-600">Participante en <a href="{{ route('campeonatos.public.share', ['campeonato' => $equipo->campeonato->id]) }}" class="text-blue-500 hover:underline">{{ $equipo->campeonato->nombre_campeonato }}</a></p>
                    </div>
                </div>
            </div>

            <!-- Pestañas de Navegación -->
            <div x-data="{ activeTab: 'jugadores' }" class="w-full">
                <div class="flex justify-center border-b-2 border-gray-200 mb-4">
                    <button @click="activeTab = 'jugadores'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'jugadores', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'jugadores'}"
                            class="py-2 px-6 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                        Plantilla
                    </button>
                    <button @click="activeTab = 'goleadores'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'goleadores', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'goleadores'}"
                            class="py-2 px-6 font-semibold text-lg border-b-4 focus:outline-none transition-colors duration-300">
                        Goleadores
                    </button>
                </div>

                <!-- Contenido de la Plantilla -->
                <div x-show="activeTab === 'jugadores'" x-cloak>
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-800 text-white uppercase text-sm leading-normal">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Jugador</th>
                                        <th class="py-3 px-4 text-center">Edad</th>
                                        <th class="py-3 px-4 text-center">Posición</th>
                                        <th class="py-3 px-4 text-center">Tarjetas</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm font-light">
                                    @forelse($jugadores as $jugador)
                                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-4 text-left flex items-center">
                                                <img src="{{ $jugador->imagen_url ?? 'https://via.placeholder.com/40' }}" class="w-10 h-10 rounded-full object-cover mr-4" alt="Foto de {{ $jugador->nombre }}">
                                                <span class="font-medium">{{ $jugador->nombre }} {{ $jugador->apellido }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center">{{ $jugador->edad }}</td>
                                            <td class="py-3 px-4 text-center">{{ $jugador->posicion ?? 'No especificada' }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="inline-block bg-yellow-400 text-white w-6 h-6 text-center rounded mr-1">{{ $jugador->tarjetas_amarillas }}</span>
                                                <span class="inline-block bg-red-600 text-white w-6 h-6 text-center rounded">{{ $jugador->tarjetas_rojas }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">No hay jugadores registrados en este equipo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Contenido de Goleadores -->
                <div x-show="activeTab === 'goleadores'" x-cloak>
                     <div class="bg-white rounded-lg shadow-md">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-800 text-white uppercase text-sm leading-normal">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Jugador</th>
                                        <th class="py-3 px-4 text-center">Goles</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm font-light">
                                    @forelse($goleadores as $goleador)
                                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-4 text-left flex items-center">
                                                <img src="{{ $goleador->imagen_url ?? 'https://via.placeholder.com/40' }}" class="w-10 h-10 rounded-full object-cover mr-4" alt="Foto de {{ $goleador->nombre }}">
                                                <span class="font-medium">{{ $goleador->nombre }} {{ $goleador->apellido }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center font-bold text-lg">{{ $goleador->goles }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-gray-500">No hay goleadores en este equipo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
