<x-app-layout>
    <header class="bg-azul text-white py-4 px-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Gestión de Usuarios</h1>
            {{-- Button to open create user modal/form --}}
            {{-- <button id="open-create-user-modal-btn" class="bg-naranja hover:bg-opacity-90 text-white px-4 py-2 rounded-lg font-medium flex items-center transition">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Crear nuevo usuario
            </button> --}}
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-8">
            <form action="{{ route('usuarios.index') }}" method="GET">
                <div class="flex rounded-lg shadow-sm">
                    <input type="text" name="search" placeholder="Buscar por nombre o email..." class="w-full px-4 py-2 border-t border-b border-l border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-azul" value="{{ request('search') }}">
                    <button type="submit" class="bg-azul text-white px-6 py-2 rounded-r-md hover:bg-opacity-90 transition">Buscar</button>
                </div>
            </form>
        </div>

        @if($users->isEmpty())
            <div class="text-center text-gray-500 py-12">
                <p class="text-lg">No se encontraron usuarios.</p>
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->role ?? 'usuario') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->plan_type ?? 'basic') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('usuarios.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                    <form action="{{ route('usuarios.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-8">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </main>
</x-app-layout>