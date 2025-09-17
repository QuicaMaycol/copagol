<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Mi Equipo en: {{ $campeonato->nombre_campeonato }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('equipos.store') }}">
                        @csrf

                        <!-- Hidden Campeonato ID -->
                        <input type="hidden" name="campeonato_id" value="{{ $campeonato->id }}">

                        <!-- Nombre del Equipo -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Equipo</label>
                            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required autofocus>
                        </div>

                        <!-- Descripcion -->
                        <div class="mt-4">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                            <textarea name="descripcion" id="descripcion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>

                        <!-- Imagen URL -->
                        <div class="mt-4">
                            <label for="imagen_url" class="block text-sm font-medium text-gray-700">URL del Escudo del Equipo</label>
                            <input type="url" name="imagen_url" id="imagen_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Pega la URL de una imagen de Google Drive, Dropbox, o cualquier servicio de alojamiento de imágenes.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('campeonatos.show', $campeonato) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Cancelar
                            </a>

                            <button type="submit" class="bg-azul text-white font-bold py-2 px-4 rounded-lg hover:bg-opacity-90">
                                Registrar Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
