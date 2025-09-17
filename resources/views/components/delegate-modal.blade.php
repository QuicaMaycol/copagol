<!-- Add Delegate Modal -->
<div id="delegate-modal" 
     x-data="{ show: false }" 
     x-show="show" 
     x-cloak
     x-on:keydown.escape.window="show = false" 
     x-on:add-delegate.window="console.log('add-delegate.window event received by modal'); show = true" 
     class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50"
     >
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4" x-on:click.away="show = false">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-2xl font-bold text-gray-800">Añadir Nuevo Delegado</h3>
            <button x-on:click="show = false" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form method="POST" action="{{ route('campeonatos.delegates.store', $campeonato) }}" class="mt-4 space-y-4">
            @csrf

            <!-- Delegate Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Delegado</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>

            <!-- Delegate Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>
            
            <!-- Delegate DNI -->
            <div>
                <label for="dni" class="block text-sm font-medium text-gray-700">DNI (Será su contraseña inicial)</label>
                <input type="text" name="dni" id="dni" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t">
                <button type="button" x-on:click="show = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Cancelar</button>
                <button type="submit" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded-lg">Añadir Delegado</button>
            </div>
        </form>
    </div>
</div>