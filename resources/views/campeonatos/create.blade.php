<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Campeonato') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('campeonatos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nombre del Torneo -->
                        <div class="mb-4">
                            <label for="nombre_torneo" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Torneo:</label>
                            <input type="text" name="nombre_torneo" id="nombre_torneo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- URL de la Imagen (Opcional) -->
                        <div class="mb-4">
                            <label for="imagen_url" class="block text-gray-700 text-sm font-bold mb-2">URL de la Imagen (Opcional):</label>
                            <input type="url" name="imagen_url" id="imagen_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: https://ejemplo.com/imagen.jpg">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Equipos Máximos -->
                            <div>
                                <label for="equipos_max" class="block text-gray-700 text-sm font-bold mb-2">Máximo de Equipos:</label>
                                <input type="number" name="equipos_max" id="equipos_max" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            </div>

                            <!-- Jugadores por Equipo -->
                            <div>
                                <label for="jugadores_por_equipo_max" class="block text-gray-700 text-sm font-bold mb-2">Máx. Jugadores por Equipo:</label>
                                <input type="number" name="jugadores_por_equipo_max" id="jugadores_por_equipo_max" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            </div>

                            <!-- Tipo de Fútbol -->
                            <div>
                                <label for="tipo_futbol" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Fútbol:</label>
                                <select name="tipo_futbol" id="tipo_futbol" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="5">Fútbol 5</option>
                                    <option value="6">Fútbol 6</option>
                                    <option value="7">Fútbol 7</option>
                                    <option value="11">Fútbol 11</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado del Torneo -->
                        <div class="mb-4">
                            <label for="estado_torneo" class="block text-gray-700 text-sm font-bold mb-2">Estado del Torneo:</label>
                            <select name="estado_torneo" id="estado_torneo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="inscripciones_abiertas">Inscripciones Abiertas</option>
                                <option value="en_curso">En Curso</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>

                        <!-- Lógica de Canchas -->
                        <div class="mb-4">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Ubicación de Partidos:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="ubicacion_tipo" value="unica" checked>
                                    <span class="ml-2">Cancha Única del Torneo</span>
                                </label>
                                <div id="cancha_unica_direccion_wrapper" class="mt-2">
                                     <label for="cancha_unica_direccion" class="block text-gray-700 text-sm font-bold mb-2">Dirección de la Cancha:</label>
                                     <input type="text" name="cancha_unica_direccion" id="cancha_unica_direccion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="ubicacion_tipo" value="equipo_local">
                                    <span class="ml-2">Cancha Propia de cada Equipo (Local y Visitante)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Privacidad -->
                        <div class="mb-4">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Privacidad del Torneo:</span>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="privacidad" value="publico" checked>
                                <span class="ml-2">Público</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" class="form-radio" name="privacidad" value="privado">
                                <span class="ml-2">Privado</span>
                            </label>
                        </div>

                        <!-- Reglamento -->
                        <div class="mb-6">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Reglamento:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="reglamento_tipo" value="pdf">
                                    <span class="ml-2">Adjuntar PDF</span>
                                </label>
                                <input type="file" name="reglamento_pdf" id="reglamento_pdf" class="mt-2" style="display: none;">
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="reglamento_tipo" value="texto" checked>
                                    <span class="ml-2">Escribir Reglamento</span>
                                </label>
                                <textarea name="reglamento_texto" id="reglamento_texto" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mt-2"></textarea>
                            </div>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Crear Campeonato
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ubicacionRadios = document.querySelectorAll('input[name="ubicacion_tipo"]');
            const canchaUnicaWrapper = document.getElementById('cancha_unica_direccion_wrapper');

            const reglamentoRadios = document.querySelectorAll('input[name="reglamento_tipo"]');
            const pdfInput = document.getElementById('reglamento_pdf');
            const textoInput = document.getElementById('reglamento_texto');

            // Lógica para mostrar/ocultar campos de cancha
            ubicacionRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    canchaUnicaWrapper.style.display = this.value === 'unica' ? 'block' : 'none';
                });
            });

            // Lógica para mostrar/ocultar campos de reglamento
            reglamentoRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    pdfInput.style.display = this.value === 'pdf' ? 'block' : 'none';
                    textoInput.style.display = this.value === 'texto' ? 'block' : 'none';
                });
            });

            // Estado inicial
            canchaUnicaWrapper.style.display = document.querySelector('input[name="ubicacion_tipo"]:checked').value === 'unica' ? 'block' : 'none';
            pdfInput.style.display = document.querySelector('input[name="reglamento_tipo"]:checked').value === 'pdf' ? 'block' : 'none';
            textoInput.style.display = document.querySelector('input[name="reglamento_tipo"]:checked').value === 'texto' ? 'block' : 'none';
        });
    </script>
</x-app-layout>
