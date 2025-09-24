<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampeonatoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController; // Added
use App\Http\Controllers\PartidoController; // Added
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/campeonatos/{campeonato}/compartir', [CampeonatoController::class, 'publicShare'])->name('campeonatos.public.share');
Route::get('/partidos/{partido}/public', [PartidoController::class, 'publicShow'])->name('partidos.public_show');
Route::get('/equipos/{equipo}/public', [EquipoController::class, 'publicShow'])->name('equipos.public_show');

Route::get('/dashboard', function () {
    return redirect()->route('campeonatos.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('campeonatos', CampeonatoController::class);
    Route::get('/campeonatos/{campeonato}/delegates/create', [CampeonatoController::class, 'createDelegateForm'])->name('campeonatos.delegates.create');
    Route::post('/campeonatos/{campeonato}/delegates', [CampeonatoController::class, 'storeDelegate'])->name('campeonatos.delegates.store');
    Route::post('/campeonatos/{campeonato}/delegates-and-teams', [CampeonatoController::class, 'storeDelegateAndTeam'])->name('campeonatos.delegates-and-teams.store');
    Route::delete('/campeonatos/{campeonato}/delegates/{user}', [CampeonatoController::class, 'destroyDelegate'])->name('campeonatos.delegates.destroy');
    Route::post('/campeonatos/{campeonato}/generate-calendar', [CampeonatoController::class, 'generateCalendar'])->name('campeonatos.generate-calendar');
    Route::patch('/campeonatos/{campeonato}/toggle-registrations', [CampeonatoController::class, 'toggleRegistrations'])->name('campeonatos.toggle-registrations');
    Route::get('/campeonatos/{campeonato}/progress', [CampeonatoController::class, 'getProgressData'])->name('campeonatos.progress');
    Route::delete('/campeonatos/{campeonato}/reset-calendar', [CampeonatoController::class, 'resetCalendar'])->name('campeonatos.reset-calendar');

    Route::post('/campeonatos/{campeonato}/update-image', [CampeonatoController::class, 'updateImage'])->name('campeonatos.updateImage');

    Route::get('/campeonatos/{campeonato}/partidos/create', [PartidoController::class, 'create'])->name('partidos.create');
    Route::post('/campeonatos/{campeonato}/partidos', [PartidoController::class, 'store'])->name('partidos.store');
    Route::get('/campeonatos/{campeonato}/equipos/{equipo}/oponentes', [PartidoController::class, 'getOponentes'])->name('partidos.oponentes');

    Route::post('/partidos/{partido}/store-result', [CampeonatoController::class, 'storeResult'])->name('partidos.store-result');
    Route::get('/partidos/{partido}/edit', [CampeonatoController::class, 'editMatch'])->name('partidos.edit');
    Route::put('/partidos/{partido}', [CampeonatoController::class, 'updateMatch'])->name('partidos.update');
    Route::delete('/partidos/{partido}', [CampeonatoController::class, 'destroyMatch'])->name('partidos.destroy');
    Route::get('/partidos/{partido}/estadisticas', [CampeonatoController::class, 'getMatchStatistics'])->name('partidos.estadisticas');
    Route::get('/partidos/{partido}/sancionados', [PartidoController::class, 'getSancionados'])->name('partidos.sancionados');

    // Rutas para la gestión de sanciones
    
    Route::post('/partidos/{partido}/finalizar-sanciones', [PartidoController::class, 'actualizarSancionesPostPartido'])->name('partidos.finalizar-sanciones');

    Route::resource('equipos', EquipoController::class)->except(['index']); // Assuming you don't need a general index of all teams
    Route::post('/equipos/{equipo}/update-image', [EquipoController::class, 'updateImage'])->name('equipos.updateImage');

    Route::get('/equipos/{equipo}/jugadores/create', [JugadorController::class, 'create'])->name('jugadores.create');
    Route::post('/equipos/{equipo}/jugadores', [JugadorController::class, 'store'])->name('jugadores.store');
    Route::resource('equipos.jugadores', JugadorController::class)->except(['index', 'create', 'store'])->parameters(['jugadores' => 'jugador']);

    // Rutas para la gestión de fases
    Route::get('/campeonatos/{campeonato}/fases/create', [FaseController::class, 'create'])->name('campeonatos.fases.create');
    Route::post('/campeonatos/{campeonato}/fases', [FaseController::class, 'store'])->name('campeonatos.fases.store');
    Route::get('/campeonatos/{campeonato}/fases/{fase}', [FaseController::class, 'show'])->name('campeonatos.fases.show');
    Route::delete('/campeonatos/{campeonato}/fases/{fase}', [FaseController::class, 'destroy'])->name('fases.destroy');

    // Rutas para la gestión de partidos dentro de una fase
    Route::get('/campeonatos/{campeonato}/fases/{fase}/partidos/create', [FaseController::class, 'createMatch'])->name('campeonatos.fases.partidos.create');
    Route::post('/campeonatos/{campeonato}/fases/{fase}/partidos', [FaseController::class, 'storeMatch'])->name('campeonatos.fases.partidos.store');

    Route::get('/campeonatos/{campeonato}/fases/{fase}/suspended-players', [CampeonatoController::class, 'getSuspendedPlayersForPhase'])->name('campeonatos.fases.suspended-players');
    Route::delete('/campeonatos/{campeonato}/matches', [CampeonatoController::class, 'deleteAllMatches'])->name('campeonatos.delete-all-matches');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/system-logs', [AdminController::class, 'systemLogs'])->name('system-logs.index');
    Route::get('/audits/campeonato/{campeonato}', [AdminController::class, 'campeonatoAudits'])->name('audits.campeonato');
    Route::get('/audits/jugador/{jugador}', [AdminController::class, 'jugadorAudits'])->name('audits.jugador');

    // User Management Routes
    Route::resource('usuarios', App\Http\Controllers\Admin\UserController::class);
});
