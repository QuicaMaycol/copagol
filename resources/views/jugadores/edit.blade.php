<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('equipos.jugadores.update', ['equipo' => $equipo->id, 'jugador' => $jugador->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Columna Izquierda: Tarjeta de Jugador -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white rounded-lg shadow-lg p-6" x-data="{ imagePreview: '{{ $jugador->imagen_url ?: 'https://static.photos/people/200x200/' . $jugador->id }}' }">
                            <div class="flex flex-col items-center">
                                <!-- Vista Previa de Imagen -->
                                <div class="w-40 h-40 rounded-full bg-gray-200 mb-4 flex items-center justify-center overflow-hidden border-4 border-gray-300">
                                    <img :src="imagePreview" alt="Foto de perfil" class="w-full h-full object-cover">
                                </div>

                                <!-- Input para Subir Imagen -->
                                <label for="imagen_jugador" class="cursor-pointer bg-copa-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg hover:bg-copa-blue-800 transition duration-300">
                                    <span>Cambiar Foto</span>
                                    <input type="file" id="imagen_jugador" name="imagen_jugador" class="hidden" @change="
                                        const reader = new FileReader();
                                        reader.onload = (e) => { imagePreview = e.target.result };
                                        reader.readAsDataURL($event.target.files[0]);
                                    ">
                                </label>
                                <x-input-error :messages="$errors->get('imagen_jugador')" class="mt-2" />

                                <div class="text-center mt-4">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $jugador->nombre }} {{ $jugador->apellido }}</h3>
                                    <p class="text-lg text-gray-600">{{ $equipo->nombre }}</p>
                                    @if($jugador->numero_camiseta)
                                        <p class="text-5xl font-mono font-extrabold text-copa-blue-900 mt-2">#{{ $jugador->numero_camiseta }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                            <h4 class="font-semibold text-gray-800 mb-2">Equipo Actual</h4>
                            <div class="flex items-center justify-center">
                                <img src="{{ $equipo->imagen_url ?: 'http://static.photos/sport/40x40/' . ($equipo->id % 10) }}" alt="Escudo del equipo" class="w-8 h-8 rounded-full mr-3">
                                <span class="text-gray-700 font-bold">{{ $equipo->nombre }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Formulario de Datos -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                            <div class="p-8 space-y-6">
                                @php
                                    $isOrganizer = Auth::user()->id === $equipo->campeonato->user_id || Auth::user()->role === 'admin';
                                    $registrationsClosed = !$equipo->campeonato->registrations_open;
                                @endphp

                                @if($registrationsClosed && !$isOrganizer)
                                    <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg border border-yellow-300">
                                        <p class="font-bold">Registros Cerrados</p>
                                        <p class="text-sm">El período de traspasos y registros ha finalizado. Los datos personales del jugador no se pueden modificar.</p>
                                    </div>
                                @endif

                                <h3 class="text-xl font-bold text-copa-blue-900 border-b-2 border-copa-blue-200 pb-2">Información Personal</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="nombre" :value="__('Nombre')" />
                                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $jugador->nombre)" required :disabled="$registrationsClosed && !$isOrganizer" />
                                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="apellido" :value="__('Apellido')" />
                                        <x-text-input id="apellido" class="block mt-1 w-full" type="text" name="apellido" :value="old('apellido', $jugador->apellido)" required :disabled="$registrationsClosed && !$isOrganizer" />
                                        <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="dni" :value="__('DNI')" />
                                        <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni', $jugador->dni)" required :disabled="$registrationsClosed && !$isOrganizer" />
                                        <x-input-error :messages="$errors->get('dni')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                                        <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento', $jugador->fecha_nacimiento ? $jugador->fecha_nacimiento->format('Y-m-d') : '')" required :disabled="$registrationsClosed && !$isOrganizer" />
                                        <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-copa-blue-900 border-b-2 border-copa-blue-200 pb-2 mt-8">Información Deportiva</h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="numero_camiseta" :value="__('Número de Camiseta')" />
                                        <x-text-input id="numero_camiseta" class="block mt-1 w-full" type="number" name="numero_camiseta" :value="old('numero_camiseta', $jugador->numero_camiseta)" />
                                        <x-input-error :messages="$errors->get('numero_camiseta')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="posicion" :value="__('Posición')" />
                                        <select id="posicion" name="posicion" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            @php
                                                $positions = [
                                                    'AR' => 'Arquero', 'DF' => 'Defensa', 'MC' => 'Mediocampista', 'DL' => 'Delantero'
                                                ];
                                            @endphp
                                            <option value="">Seleccione una posición</option>
                                            @foreach ($positions as $value => $label)
                                                <option value="{{ $value }}" {{ old('posicion', $jugador->posicion) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('posicion')" class="mt-2" />
                                    </div>
                                </div>

                                @if($isOrganizer)
                                    <h3 class="text-xl font-bold text-red-600 border-b-2 border-red-200 pb-2 mt-8">Estadísticas y Sanciones (Solo Admin)</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                        <div>
                                            <x-input-label for="goles" :value="__('Goles')" />
                                            <x-text-input id="goles" class="block mt-1 w-full" type="number" name="goles" :value="old('goles', $jugador->goles)" required :disabled="!$isOrganizer" />
                                        </div>
                                        <div>
                                            <x-input-label for="tarjetas_amarillas" :value="__('Tarjetas Amarillas')" />
                                            <x-text-input id="tarjetas_amarillas" class="block mt-1 w-full" type="number" name="tarjetas_amarillas" :value="old('tarjetas_amarillas', $jugador->tarjetas_amarillas)" required :disabled="!$isOrganizer" />
                                        </div>
                                        <div>
                                            <x-input-label for="tarjetas_rojas" :value="__('Tarjetas Rojas')" />
                                            <x-text-input id="tarjetas_rojas" class="block mt-1 w-full" type="number" name="tarjetas_rojas" :value="old('tarjetas_rojas', $jugador->tarjetas_rojas)" required :disabled="!$isOrganizer" />
                                        </div>
                                        <div>
                                            <x-input-label for="valoracion_general" :value="__('Valoración (1-100)')" />
                                            <x-text-input id="valoracion_general" class="block mt-1 w-full" type="number" name="valoracion_general" :value="old('valoracion_general', $jugador->valoracion_general)" required min="1" max="100" :disabled="!$isOrganizer" />
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <x-input-label for="suspendido" :value="__('Suspendido')" class="mr-3"/>
                                            <input type="checkbox" name="suspendido" id="suspendido" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('suspendido', $jugador->suspendido) ? 'checked' : '' }} @if(!$isOrganizer) disabled @endif>
                                        </div>
                                    </div>
                                @endif

                                <!-- Botones de Acción -->
                                <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                                    <a href="{{ route('equipos.show', $equipo->id) }}" class="text-sm text-gray-600 hover:text-gray-900 uppercase font-semibold">
                                        {{ __('Cancelar') }}
                                    </a>
                                    <x-primary-button class="ml-4">
                                        {{ __('Actualizar Jugador') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
