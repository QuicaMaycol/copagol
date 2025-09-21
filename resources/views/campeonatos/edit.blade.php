<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Campeonato') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('campeonatos.update', $campeonato) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nombre del Torneo -->
                        <div class="mb-4">
                            <label for="nombre_torneo" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Torneo:</label>
                            <input type="text" name="nombre_torneo" id="nombre_torneo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('nombre_torneo', $campeonato->nombre_torneo) }}" required>
                        </div>

                        

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Equipos Máximos -->
                            <div>
                                <label for="equipos_max" class="block text-gray-700 text-sm font-bold mb-2">Máximo de Equipos:</label>
                                <input type="number" name="equipos_max" id="equipos_max" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('equipos_max', $campeonato->equipos_max) }}" required>
                            </div>

                            <!-- Jugadores por Equipo -->
                            <div>
                                <label for="jugadores_por_equipo_max" class="block text-gray-700 text-sm font-bold mb-2">Máx. Jugadores por Equipo:</label>
                                <input type="number" name="jugadores_por_equipo_max" id="jugadores_por_equipo_max" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('jugadores_por_equipo_max', $campeonato->jugadores_por_equipo_max) }}" required>
                            </div>

                            <!-- Tipo de Fútbol -->
                            <div>
                                <label for="tipo_futbol" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Fútbol:</label>
                                <select name="tipo_futbol" id="tipo_futbol" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="5" {{ old('tipo_futbol', $campeonato->tipo_futbol) == '5' ? 'selected' : '' }}>Fútbol 5</option>
                                    <option value="6" {{ old('tipo_futbol', $campeonato->tipo_futbol) == '6' ? 'selected' : '' }}>Fútbol 6</option>
                                    <option value="7" {{ old('tipo_futbol', $campeonato->tipo_futbol) == '7' ? 'selected' : '' }}>Fútbol 7</option>
                                    <option value="11" {{ old('tipo_futbol', $campeonato->tipo_futbol) == '11' ? 'selected' : '' }}>Fútbol 11</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado del Torneo -->
                        <div class="mb-4">
                            <label for="estado_torneo" class="block text-gray-700 text-sm font-bold mb-2">Estado del Torneo:</label>
                            <select name="estado_torneo" id="estado_torneo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="inscripciones_abiertas" {{ old('estado_torneo', $campeonato->estado_torneo) == 'inscripciones_abiertas' ? 'selected' : '' }}>Inscripciones Abiertas</option>
                                <option value="en_curso" {{ old('estado_torneo', $campeonato->estado_torneo) == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                <option value="finalizado" {{ old('estado_torneo', $campeonato->estado_torneo) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                            </select>
                        </div>

                        <!-- Lógica de Canchas -->
                        <div class="mb-4">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Ubicación de Partidos:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="ubicacion_tipo" value="unica" {{ old('ubicacion_tipo', $campeonato->ubicacion_tipo) == 'unica' ? 'checked' : '' }}>
                                    <span class="ml-2">Cancha Única del Torneo</span>
                                </label>
                                <div id="cancha_unica_direccion_wrapper" class="mt-2">
                                     <label for="cancha_unica_direccion" class="block text-gray-700 text-sm font-bold mb-2">Dirección de la Cancha:</label>
                                     <input type="text" name="cancha_unica_direccion" id="cancha_unica_direccion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('cancha_unica_direccion', $campeonato->cancha_unica_direccion) }}">
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="ubicacion_tipo" value="equipo_local" {{ old('ubicacion_tipo', $campeonato->ubicacion_tipo) == 'equipo_local' ? 'checked' : '' }}>
                                    <span class="ml-2">Cancha Propia de cada Equipo (Local y Visitante)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Privacidad -->
                        <div class="mb-4">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Privacidad del Torneo:</span>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="privacidad" value="publico" {{ old('privacidad', $campeonato->privacidad) == 'publico' ? 'checked' : '' }}>
                                <span class="ml-2">Público</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" class="form-radio" name="privacidad" value="privado" {{ old('privacidad', $campeonato->privacidad) == 'privado' ? 'checked' : '' }}>
                                <span class="ml-2">Privado</span>
                            </label>
                        </div>

                        <!-- Reglamento -->
                        <div class="mb-6">
                            <span class="block text-gray-700 text-sm font-bold mb-2">Reglamento:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="reglamento_tipo" value="pdf" {{ old('reglamento_tipo', $campeonato->reglamento_tipo) == 'pdf' ? 'checked' : '' }}>
                                    <span class="ml-2">Adjuntar PDF</span>
                                </label>
                                <input type="file" name="reglamento_pdf" id="reglamento_pdf" class="mt-2" style="display: {{ old('reglamento_tipo', $campeonato->reglamento_tipo) == 'pdf' ? 'block' : 'none' }};">
                            </div>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="reglamento_tipo" value="texto" {{ old('reglamento_tipo', $campeonato->reglamento_tipo) == 'texto' ? 'checked' : '' }}>
                                    <span class="ml-2">Escribir Reglamento</span>
                                </label>
                                <textarea name="reglamento_texto" id="reglamento_texto" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mt-2" style="display: {{ old('reglamento_tipo', $campeonato->reglamento_tipo) == 'texto' ? 'block' : 'none' }};">{{ old('reglamento_texto', $campeonato->reglamento_texto) }}</textarea>
                            </div>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('campeonatos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                Cancelar
                            </a>
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.audits.campeonato', $campeonato) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                    Ver Historial
                                </a>
                            @endif
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Actualizar Campeonato
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
            const canchaUnicaInput = document.getElementById('cancha_unica_direccion');

            const reglamentoRadios = document.querySelectorAll('input[name="reglamento_tipo"]');
            const pdfInput = document.getElementById('reglamento_pdf');
            const textoInput = document.getElementById('reglamento_texto');

            // Lógica para mostrar/ocultar campos de cancha
            function toggleCanchaUnica() {
                const isUnica = document.querySelector('input[name="ubicacion_tipo"]:checked').value === 'unica';
                canchaUnicaWrapper.style.display = isUnica ? 'block' : 'none';
                canchaUnicaInput.required = isUnica; // Make required if 'unica' is selected
            }

            ubicacionRadios.forEach(radio => {
                radio.addEventListener('change', toggleCanchaUnica);
            });

            // Lógica para mostrar/ocultar campos de reglamento
            function toggleReglamentoFields() {
                const reglamentoTipo = document.querySelector('input[name="reglamento_tipo"]:checked').value;
                pdfInput.style.display = reglamentoTipo === 'pdf' ? 'block' : 'none';
                textoInput.style.display = reglamentoTipo === 'texto' ? 'block' : 'none';
                pdfInput.required = reglamentoTipo === 'pdf';
                textoInput.required = reglamentoTipo === 'texto';
            }

            reglamentoRadios.forEach(radio => {
                radio.addEventListener('change', toggleReglamentoFields);
            });

            // Estado inicial
            toggleCanchaUnica();
            toggleReglamentoFields();
        });
    </script>
</x-app-layout>