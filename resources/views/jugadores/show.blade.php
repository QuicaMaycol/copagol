<div class="p-6">
    <div class="flex items-center">
        <img src="{{ $jugador->imagen_url ?? 'http://static.photos/people/150x150/' . $jugador->id }}" alt="{{ $jugador->nombre }} {{ $jugador->apellido }}" class="w-36 h-36 rounded-full object-cover mb-4 border-4 border-blue-500 shadow-lg">
        <div class="ml-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Detalles del Jugador</h3>
            <p class="text-gray-600"><strong>Nombre Completo:</strong> {{ $jugador->nombre }} {{ $jugador->apellido }}</p>
            <p class="text-gray-600"><strong>DNI:</strong> {{ $jugador->dni }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <p class="text-gray-600"><strong>Número de Camiseta:</strong> {{ $jugador->numero_camiseta }}</p>
            <p class="text-gray-600"><strong>Posición:</strong> {{ $jugador->posicion }}</p>
            <p class="text-gray-600"><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($jugador->fecha_nacimiento)->format('d/m/Y') }}</p>
            <p class="text-gray-600"><strong>Equipo:</strong> {{ $equipo->nombre }}</p>
        </div>
        <div>
            <p class="text-gray-600"><strong>Goles:</strong> {{ $jugador->goles }}</p>
            <p class="text-gray-600"><strong>Tarjetas Amarillas:</strong> {{ $jugador->tarjetas_amarillas }}</p>
            <p class="text-gray-600"><strong>Tarjetas Rojas:</strong> {{ $jugador->tarjetas_rojas }}</p>
            <p class="text-gray-600"><strong>Suspendido:</strong> {{ $jugador->suspendido ? 'Sí' : 'No' }}</p>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-4">
        <a href="{{ route('equipos.jugadores.edit', ['equipo' => $equipo->id, 'jugadore' => $jugador->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            Editar
        </a>
        <form action="{{ route('equipos.jugadores.destroy', ['equipo' => $equipo->id, 'jugadore' => $jugador->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este jugador?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Eliminar
            </button>
        </form>
    </div>
</div>
