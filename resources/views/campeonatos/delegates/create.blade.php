<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Añadir Delegado al Campeonato: {{ $campeonato->nombre_torneo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('campeonatos.delegates.store', $campeonato) }}" class="space-y-6">
                        @csrf

                        <!-- Delegate Name -->
                        <div>
                            <x-input-label for="name" :value="'Nombre del Delegado'" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Delegate Email -->
                        <div>
                            <x-input-label for="email" :value="'Correo Electrónico'" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        
                        <!-- Delegate DNI -->
                        <div>
                            <x-input-label for="dni" :value="'DNI (Será su contraseña inicial)'" />
                            <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" required autocomplete="dni" />
                            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('campeonatos.show', $campeonato) }}#delegates-section" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                {{ 'Cancelar' }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ 'Añadir Delegado' }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>