<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Crear Nueva Fase para {{ $campeonato->nombre_torneo }}</h1>

                    <form action="{{ route('campeonatos.fases.store', $campeonato) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Fase</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="nombre" name="nombre" required>
                        </div>
                        <div>
                            <label for="orden" class="block text-sm font-medium text-gray-700">Orden</label>
                            <input type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="orden" name="orden" value="1" required>
                        </div>
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Fase</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" id="tipo" name="tipo" required>
                                <option value="grupos">Fase de Grupos</option>
                                <option value="eliminatoria">Fase Eliminatoria</option>
                            </select>
                        </div>
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" id="estado" name="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="activa">Activa</option>
                                <option value="finalizada">Finalizada</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-copa-blue-700 hover:bg-copa-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-copa-blue-500">
                            Crear Fase
                        </button>
                        <a href="{{ route('campeonatos.show', $campeonato) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-4">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
