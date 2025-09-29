<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Script para el componente de recorte de imagen -->
            <script>
                function imageCropper(initialImageUrl) {
                    return {
                        imagePreview: initialImageUrl,
                        showCropper: false,
                        cropper: null,
                        
                        handleFileSelect(event) {
                            const file = event.target.files[0];
                            if (!file || !file.type.startsWith('image/')) return;

                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.$refs.cropperImage.src = e.target.result;
                                this.showCropper = true;
                                this.$nextTick(() => {
                                    if (this.cropper) this.cropper.destroy();
                                    // Asegúrate de que `Cropper` está disponible globalmente (lo hicimos en app.js)
                                    this.cropper = new Cropper(this.$refs.cropperImage, {
                                        aspectRatio: 1,
                                        viewMode: 1,
                                        background: false,
                                    });
                                });
                            };
                            reader.readAsDataURL(file);
                        },

                        cropImage() {
                            if (!this.cropper) return;

                            this.cropper.getCroppedCanvas({
                                width: 512,
                                height: 512,
                                imageSmoothingQuality: 'high',
                            }).toBlob((blob) => {
                                this.imagePreview = URL.createObjectURL(blob);

                                const file = new File([blob], "cropped_image.png", { type: blob.type });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                
                                document.getElementById('imagen_jugador').files = dataTransfer.files;

                                this.showCropper = false;
                                this.cropper.destroy();
                                this.cropper = null;
                            }, 'image/png');
                        },

                        cancelCrop() {
                            this.showCropper = false;
                            if (this.cropper) {
                                this.cropper.destroy();
                                this.cropper = null;
                            }
                            document.getElementById('imagen_jugador').value = '';
                        }
                    }
                }
            </script>

            <div x-data="imageCropper('{{ $jugador->imagen_path ? asset('storage/' . $jugador->imagen_path) : asset('img/logo.png') }}')" x-cloak>
                <!-- Cropper Modal -->
                <div x-show="showCropper" @keydown.escape.window="cancelCrop()" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full">
                        <h3 class="text-xl font-bold mb-4">Recortar Imagen</h3>
                        <div class="max-h-96 w-full">
                            <img x-ref="cropperImage" class="max-w-full">
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="cancelCrop()" class="px-4 py-2 bg-gray-300 rounded-md text-sm font-semibold">Cancelar</button>
                            <button type="button" @click="cropImage()" class="px-4 py-2 bg-copa-blue-700 text-white rounded-md text-sm font-semibold">Aceptar y Recortar</button>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('equipos.jugadores.update', ['equipo' => $equipo->id, 'jugador' => $jugador->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Columna Izquierda: Tarjeta de Jugador -->
                        <div class="md:col-span-1 space-y-6">
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <div class="flex flex-col items-center">
                                    <!-- Vista Previa de Imagen -->
                                    <div class="w-40 h-40 rounded-full bg-gray-200 mb-4 flex items-center justify-center overflow-hidden border-4 border-gray-300">
                                        <img :src="imagePreview" alt="Foto de perfil" class="w-full h-full object-cover">
                                    </div>

                                    <!-- Input para Subir Imagen -->
                                    <label for="imagen_jugador" class="cursor-pointer bg-copa-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg hover:bg-copa-blue-800 transition duration-300">
                                        <span>Cambiar Foto</span>
                                        <input type="file" id="imagen_jugador" name="imagen_jugador" class="hidden" @change="handleFileSelect($event)" accept="image/*">
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
                                    <img src="{{ $equipo->imagen_path ? asset('storage/' . $equipo->imagen_path) : asset('img/logo.png') }}" alt="Escudo del equipo" class="w-8 h-8 rounded-full mr-3">
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
                                                <option value="">Seleccione una posición</option>
                                                @foreach ($positions as $group => $options)
                                                    <optgroup label="{{ $group }}">
                                                        @foreach ($options as $value => $label)
                                                            <option value="{{ $value }}" {{ old('posicion', $jugador->posicion) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
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
    </div>
</x-app-layout>