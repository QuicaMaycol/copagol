<!-- Error Modal -->
@if (session('error'))
<div id="error-modal" x-data="{ show: true }" x-show="show" @keydown.escape.window="show = false" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4 text-center">
        <div class="flex justify-center items-center mx-auto w-12 h-12 rounded-full bg-red-100">
            <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mt-4">Acceso Denegado</h3>
        <p class="text-gray-600 mt-2">{{ session('error') }}</p>
        <div class="mt-6">
            <button @click="show = false" class="bg-rojo hover:bg-opacity-90 text-white font-bold py-2 px-6 rounded-lg">
                Cerrar
            </button>
        </div>
    </div>
</div>
<script>
    // Re-initialize Feather Icons if the modal is shown
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('error-modal')) {
            feather.replace();
        }
    });
</script>
@endif
