<x-modal name="edit-team-modal" :show="$errors->teamUpdating->isNotEmpty()" focusable>
    <form method="post" action="{{ route('equipos.update', $equipo) }}" class="p-6">
        @csrf
        @method('patch') {{-- Use patch for updates --}}

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Editar Información del Equipo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Actualiza el nombre, descripción y URL del escudo de tu equipo.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="nombre" :value="__('Nombre del Equipo')" />
            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $equipo->nombre)" required autofocus autocomplete="nombre" />
            <x-input-error :messages="$errors->teamUpdating->get('nombre')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="descripcion" :value="__('Descripción (Opcional)')" />
            <x-textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full">{{ old('descripcion', $equipo->descripcion) }}</x-textarea>
            <x-input-error :messages="$errors->teamUpdating->get('descripcion')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="imagen_url" :value="__('URL del Escudo del Equipo')" />
            <x-text-input id="imagen_url" name="imagen_url" type="url" class="mt-1 block w-full" :value="old('imagen_url', $equipo->imagen_url)" autocomplete="imagen_url" />
            <x-input-error :messages="$errors->teamUpdating->get('imagen_url')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pega la URL de una imagen de Google Drive, Dropbox, o cualquier servicio de alojamiento de imágenes.</p>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Guardar Cambios') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>