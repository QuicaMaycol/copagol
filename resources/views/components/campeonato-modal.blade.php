<!-- Create/Edit Campeonato Modal -->
<div id="campeonato-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="modal-title" class="text-2xl font-bold text-azul">Crear Campeonato</h3>
            <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form id="campeonato-form" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            <!-- Nombre del Torneo -->
            <div>
                <label for="nombre_torneo" class="block text-sm font-medium text-gray-700">Nombre del Torneo</label>
                <input type="text" name="nombre_torneo" id="nombre_torneo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Max Equipos -->
                <div>
                    <label for="equipos_max" class="block text-sm font-medium text-gray-700">Máximo de Equipos</label>
                    <input type="number" name="equipos_max" id="equipos_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <!-- Max Jugadores por equipo -->
                <div>
                    <label for="jugadores_por_equipo_max" class="block text-sm font-medium text-gray-700">Máximo de Jugadores por Equipo</label>
                    <input type="number" name="jugadores_por_equipo_max" id="jugadores_por_equipo_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
            </div>

            <!-- Tipo de Futbol -->
            <div>
                <label for="tipo_futbol" class="block text-sm font-medium text-gray-700">Tipo de Fútbol</label>
                <select name="tipo_futbol" id="tipo_futbol" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="5">Fútbol 5</option>
                    <option value="7">Fútbol 7</option>
                    <option value="11">Fútbol 11</option>
                </select>
            </div>

            <!-- Ubicacion -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                <div class="mt-2 space-y-2">
                    <div class="flex items-center">
                        <input id="ubicacion_unica" type="radio" name="ubicacion_tipo" value="unica" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="ubicacion_unica" class="ml-3 block text-sm text-gray-700">Cancha Única del Torneo</label>
                    </div>
                    <div class="flex items-center">
                        <input id="ubicacion_equipo_local" type="radio" name="ubicacion_tipo" value="equipo_local" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="ubicacion_equipo_local" class="ml-3 block text-sm text-gray-700">Cancha Propia de cada Equipo (Local y Visitante)</label>
                    </div>
                </div>
                <input type="text" name="cancha_unica_direccion" id="cancha_unica_direccion" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Dirección de la cancha única">
            </div>

            <!-- Privacidad -->
            <div>
                <label for="privacidad" class="block text-sm font-medium text-gray-700">Privacidad</label>
                <select name="privacidad" id="privacidad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="publico">Público</option>
                    <option value="privado">Privado</option>
                </select>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t">
                <button type="button" id="cancel-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Cancelar</button>
                <button type="submit" id="submit-btn" class="bg-naranja hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded-lg">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('campeonato-modal');
        const openCreateBtn = document.getElementById('open-create-modal-btn');
        const closeBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const form = document.getElementById('campeonato-form');
        const modalTitle = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');

        // --- Open for Create ---
        if (openCreateBtn) {
            openCreateBtn.addEventListener('click', () => {
                form.reset();
                form.action = "{{ route('campeonatos.store') }}";
                formMethod.value = 'POST';
                modalTitle.textContent = 'Crear Campeonato';
                modal.classList.remove('hidden');
            });
        }

        // --- Open for Edit ---
        document.querySelectorAll('.open-edit-modal-btn').forEach(button => {
            button.addEventListener('click', () => {
                const campeonato = JSON.parse(button.dataset.campeonato);
                const updateUrl = button.dataset.updateUrl;

                form.reset();
                form.action = updateUrl;
                formMethod.value = 'PUT';
                modalTitle.textContent = 'Editar Campeonato';

                // Populate form
                document.getElementById('nombre_torneo').value = campeonato.nombre_torneo;
                document.getElementById('equipos_max').value = campeonato.equipos_max;
                document.getElementById('jugadores_por_equipo_max').value = campeonato.jugadores_por_equipo_max;
                document.getElementById('tipo_futbol').value = campeonato.tipo_futbol;
                document.getElementById('privacidad').value = campeonato.privacidad;
                
                const ubicacionTipo = document.querySelector(`input[name="ubicacion_tipo"][value="${campeonato.ubicacion_tipo}"]`);
                if(ubicacionTipo) ubicacionTipo.checked = true;

                const canchaUnica = document.getElementById('cancha_unica_direccion');
                canchaUnica.value = campeonato.cancha_unica_direccion || '';
                canchaUnica.style.display = campeonato.ubicacion_tipo === 'unica' ? 'block' : 'none';

                modal.classList.remove('hidden');
            });
        });

        // --- Close Modal ---
        const closeModal = () => {
            modal.classList.add('hidden');
        };
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // --- Logic for Ubicacion radio buttons ---
        const canchaUnicaInput = document.getElementById('cancha_unica_direccion');
        document.querySelectorAll('input[name="ubicacion_tipo"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'unica') {
                    canchaUnicaInput.style.display = 'block';
                } else {
                    canchaUnicaInput.style.display = 'none';
                }
            });
        });
    });
</script>
