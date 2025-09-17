<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Jugador para ') }} {{ $equipo->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('jugadores.store', ['equipo' => $equipo->id]) }}">
                        @csrf

                        <!-- Nombre -->
                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <!-- Apellido -->
                        <div class="mt-4">
                            <x-input-label for="apellido" :value="__('Apellido')" />
                            <x-text-input id="apellido" class="block mt-1 w-full" type="text" name="apellido" :value="old('apellido')" required />
                            <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
                        </div>

                        <!-- DNI -->
                        <div class="mt-4">
                            <x-input-label for="dni" :value="__('DNI')" />
                            <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" required />
                            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
                        </div>

                        <!-- Número de Camiseta -->
                        <div class="mt-4">
                            <x-input-label for="numero_camiseta" :value="__('Número de Camiseta')" />
                            <x-text-input id="numero_camiseta" class="block mt-1 w-full" type="number" name="numero_camiseta" :value="old('numero_camiseta')" />
                            <x-input-error :messages="$errors->get('numero_camiseta')" class="mt-2" />
                        </div>

                        <!-- Posición -->
                        <div class="mt-4">
                            <x-input-label for="posicion" :value="__('Posición (Seleccione una o varias)')" />
                            @php
                                $positions = [
                                    'Posiciones Únicas' => [
                                        'AR' => '🟢 AR - Arquero',
                                        'DF' => '🔵 DF - Defensa',
                                        'MC' => '🔵 MC - Marcador Central',
                                        'LI' => '🔵 LI - Lateral Izquierdo',
                                        'LD' => '🔵 LD - Lateral Derecho',
                                        'MP' => '🟡 MP - Medio Campo',
                                        'MCD' => '🟡 MCD - Medio Campo Defensivo',
                                        'MCO' => '🟡 MCO - Medio Campo Ofensivo',
                                        'DL' => '🔴 DL - Delantero',
                                        'EXI' => '🔴 EXI - Extremo Izquierdo',
                                        'EXD' => '🔴 EXD - Extremo Derecho',
                                    ],
                                    'Combinaciones (2 Posiciones)' => [
                                        'DF/MC' => '⚪️ DF/MC - Defensa o Marcador Central',
                                        'LI/LD' => '⚪️ LI/LD - Lateral por ambas bandas',
                                        'MP/MCD' => '⚪️ MP/MCD - Medio Campo o Contención',
                                        'MP/MCO' => '⚪️ MP/MCO - Medio Campo o Creación',
                                        'DL/EX' => '⚪️ DL/EX - Delantero o Extremo',
                                        'DF/MP' => '⚪️ DF/MP - Defensa o Medio Campo',
                                    ],
                                    'Combinaciones (3 Posiciones)' => [
                                        'DF/MC/LI' => '⚪️ DF/MC/LI - Defensa, Central o Lateral Izquierdo',
                                        'MP/MCD/MCO' => '⚪️ MP/MCD/MCO - Cualquier rol en Medio Campo',
                                        'DL/EXI/EXD' => '⚪️ DL/EXI/EXD - Cualquier rol en Delantera',
                                        'DF/DL/MP' => '⚪️ DF/DL/MP - Jugador Polivalente (Defensa, Delantero, Medio)',
                                    ]
                                ];
                            @endphp
                            <select id="posicion" name="posicion" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Seleccione una posición</option>
                                @foreach ($positions as $group => $options)
                                    <optgroup label="{{ $group }}">
                                        @foreach ($options as $value => $label)
                                            <option value="{{ $value }}" {{ old('posicion') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('posicion')" class="mt-2" />
                        </div>

                        <!-- Imagen URL -->
                        <div class="mt-4">
                            <x-input-label for="imagen_url" :value="__('URL de la Imagen de Perfil')" />
                            <x-text-input id="imagen_url" class="block mt-1 w-full" type="url" name="imagen_url" :value="old('imagen_url')" />
                            <p class="mt-2 text-sm text-gray-500">Pega la URL de una imagen de Google Drive, Dropbox, o cualquier servicio de alojamiento de imágenes.</p>
                            <x-input-error :messages="$errors->get('imagen_url')" class="mt-2" />
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="mt-4">
                            <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                            <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
                            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('equipos.show', $equipo->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Registrar Jugador') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
