@props(['campeonato'])

<div x-data="{ open: false, step: 1 }" @open-add-delegate-team-modal.window="open = true; step = 1" x-show="open" style="display: none;"
    x-on:keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"
            aria-hidden="true"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">

            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                Agregar Nuevo Equipo
            </h3>

            <form method="POST" action="{{ route('campeonatos.delegates-and-teams.store', $campeonato) }}" class="space-y-6 mt-4">
                @csrf
                <input type="hidden" name="campeonato_id" value="{{ $campeonato->id }}">

                <!-- Step 1: Delegate Info -->
                <div x-show="step === 1">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Paso 1: Datos del Delegado</h4>
                    <!-- Delegate Name -->
                    <div>
                        <x-input-label for="delegate_name" :value="'Nombre del Delegado'" />
                        <x-text-input id="delegate_name" class="block mt-1 w-full" type="text" name="delegate_name"
                            :value="old('delegate_name')" required autocomplete="name" />
                        <x-input-error :messages="$errors->get('delegate_name')" class="mt-2" />
                    </div>

                    <!-- Delegate Email -->
                    <div class="mt-4">
                        <x-input-label for="delegate_email" :value="'Correo Electrónico'" />
                        <x-text-input id="delegate_email" class="block mt-1 w-full" type="email" name="delegate_email"
                            :value="old('delegate_email')" required autocomplete="email" />
                        <x-input-error :messages="$errors->get('delegate_email')" class="mt-2" />
                    </div>

                    <!-- Delegate DNI -->
                    <div class="mt-4">
                        <x-input-label for="delegate_dni" :value="'DNI (Será su contraseña inicial)'" />
                        <x-text-input id="delegate_dni" class="block mt-1 w-full" type="text" name="delegate_dni"
                            :value="old('delegate_dni')" required />
                        <x-input-error :messages="$errors->get('delegate_dni')" class="mt-2" />
                    </div>
                </div>

                <!-- Step 2: Team Info -->
                <div x-show="step === 2" style="display: none;">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Paso 2: Datos del Equipo</h4>
                    <!-- Team Name -->
                    <div>
                        <label for="team_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Equipo</label>
                        <input type="text" name="team_name" id="team_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            required>
                    </div>

                    <!-- Team Description -->
                    <div class="mt-4">
                        <label for="team_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                        <textarea name="team_description" id="team_description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <div x-show="step === 1">
                        <button type="button" @click="open = false"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancelar
                        </button>
                    </div>

                    <div x-show="step === 2" style="display: none;">
                        <button type="button" @click="step = 1"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Atrás
                        </button>
                    </div>

                    <div>
                        <button type="button" x-show="step === 1" @click="step = 2"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Siguiente
                        </button>

                        <button type="submit" x-show="step === 2" style="display: none;"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Crear Equipo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
