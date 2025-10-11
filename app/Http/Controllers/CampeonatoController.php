<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Partido;
use App\Models\Equipo;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PartidoController;

class CampeonatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Campeonato::with(['organizador', 'equipos'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('nombre_torneo', 'like', '%' . $searchTerm . '%');
        }

        $campeonatos = $query->paginate(10);

        return view('campeonatos.index', compact('campeonatos'));
    }

    public function publicShare(Campeonato $campeonato)
    {
        // Cargar relaciones para optimizar consultas
        $campeonato->load('equipos', 'partidos.estadisticasJugadores.jugador', 'organizador');

        // Lógica para obtener goleadores, tabla de posiciones, etc.
        $goleadores = $this->getGoleadores($campeonato);
        $tablaPosiciones = $this->getTablaPosiciones($campeonato);

        // Find the featured match (next upcoming match)
        $featuredMatch = $campeonato->partidos()
            ->with('equipoLocal', 'equipoVisitante')
            ->where('estado', '!=', 'finalizado')
            ->where('fecha_partido', '>=', now())
            ->orderBy('fecha_partido', 'asc')
            ->first();

        // --- Lógica para calcular restingTeamsByJornada ---
        $restingTeamsByJornada = [];
        $teams = $campeonato->equipos;
        $numTeams = $teams->count();
        $partidos = $campeonato->partidos->sortBy('jornada');

        if ($numTeams % 2 !== 0) { // Solo si hay un número impar de equipos, un equipo descansa
            $maxJornada = $partidos->max('jornada');
            for ($jornada = 1; $jornada <= $maxJornada; $jornada++) {
                $playingTeamIdsInJornada = $partidos->where('jornada', $jornada)
                                                    ->pluck('equipo_local_id')
                                                    ->merge($partidos->where('jornada', $jornada)->pluck('equipo_visitante_id'))
                                                    ->filter() // Eliminar nulos si los hay
                                                    ->unique()
                                                    ->toArray();
                
                $allRealTeamIds = $teams->pluck('id')->toArray();
                $restingTeamIds = array_diff($allRealTeamIds, $playingTeamIdsInJornada);

                if (!empty($restingTeamIds)) {
                    $restingTeam = \App\Models\Equipo::find(reset($restingTeamIds));
                    if ($restingTeam) {
                        $restingTeamsByJornada[$jornada] = $restingTeam->nombre;
                    }
                }
            }
        }
        // --- Fin de la lógica ---

        return view('campeonatos.public_share', [
            'campeonato' => $campeonato,
            'goleadores' => $goleadores,
            'tablaPosiciones' => $tablaPosiciones,
            'featuredMatch' => $featuredMatch,
            'restingTeamsByJornada' => $restingTeamsByJornada, // Pasar la variable a la vista
        ]);
    }

    // Métodos privados para calcular estadísticas
    private function getGoleadores(Campeonato $campeonato)
    {
        return \App\Models\Jugador::whereHas('equipo', function ($query) use ($campeonato) {
            $query->where('campeonato_id', $campeonato->id);
        })
        ->where('goles', '>', 0)
        ->with('equipo')
        ->withCount('estadisticas as partidos_jugados')
        ->orderByDesc('goles')
        ->get();
    }

    private function getTablaPosiciones(Campeonato $campeonato)
    {
        return $campeonato->getStandings();
    }

    private function getFairPlay(Campeonato $campeonato)
    {
        $equipos = $campeonato->equipos()->with('jugadores')->get();

        $fairPlayData = $equipos->map(function ($equipo) {
            $totalAmarillas = $equipo->jugadores->sum('tarjetas_amarillas');
            $totalRojas = $equipo->jugadores->sum('tarjetas_rojas');
            
            // Assuming 1 point per yellow card and 3 per red card
            $puntosFairPlay = 100 - ($totalAmarillas * 1) - ($totalRojas * 3);

            return [
                'nombre' => $equipo->nombre,
                'amarillas' => $totalAmarillas,
                'rojas' => $totalRojas,
                'puntos' => $puntosFairPlay,
            ];
        });

        return $fairPlayData->sortByDesc('puntos');
    }

    private function getSancionadosData(Campeonato $campeonato)
    {
        $jugadores = \App\Models\Jugador::whereHas('equipo', function ($query) use ($campeonato) {
            $query->where('campeonato_id', $campeonato->id);
        })
        ->with('equipo')
        ->get();

        $sancionadosPorRoja = $jugadores->filter(function ($jugador) {
            // Jugador tiene tarjeta roja y está actualmente marcado como suspendido
            return $jugador->tarjetas_rojas > 0 && $jugador->suspendido;
        });

        $sancionadosPorAmarillas = $jugadores->filter(function ($jugador) {
            // Jugador está suspendido pero no tiene tarjetas rojas, implicando acumulación de amarillas
            return $jugador->suspendido && $jugador->tarjetas_rojas == 0;
        });

        $apercibidos = $jugadores->filter(function ($jugador) {
            // El jugador tiene un número impar de tarjetas amarillas y no está actualmente suspendido
            // Se asume que la suspensión es cada 2 amarillas.
            return $jugador->tarjetas_amarillas > 0 && ($jugador->tarjetas_amarillas % 2 != 0) && !$jugador->suspendido;
        });

        return [
            'sancionadosPorRoja' => $sancionadosPorRoja,
            'sancionadosPorAmarillas' => $sancionadosPorAmarillas,
            'apercibidos' => $apercibidos,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('campeonatos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_torneo' => 'required|string|max:255',
            'imagen_path' => 'nullable|url',
            'equipos_max' => 'required|integer|min:2',
            'jugadores_por_equipo_max' => 'required|integer|min:1',
            'tipo_futbol' => 'required|in:5,7,11',
            'ubicacion_tipo' => 'required|in:unica,equipo_local',
            'cancha_unica_direccion' => 'nullable|string|max:255',
            'privacidad' => 'required|in:publico,privado',
        ]);

        $campeonato = new Campeonato($validatedData);
        $campeonato->user_id = Auth::id();
        $campeonato->save();

        SystemLog::add('campeonato.created', "El usuario {" . Auth::user()->name . "} creó el campeonato '{$campeonato->nombre_torneo}'", $campeonato);

        return Redirect::route('campeonatos.index')->with('success', 'Campeonato creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campeonato $campeonato)
    {
        $user = Auth::user();

        if ($campeonato->privacidad === 'privado') {
            $isDelegate = $campeonato->delegates->pluck('id')->contains($user->id);
            $isMember = $campeonato->user_id === $user->id || $isDelegate;

            if ($user->role !== 'admin' && !$isMember) {
                return Redirect::route('campeonatos.index')->with('error', 'No tienes permiso para ver este campeonato privado.');
            }
        }

        $campeonato->load('organizador', 'delegates', 'partidos.equipoLocal', 'partidos.equipoVisitante', 'fases'); // Load organizador, delegates, matches, and phases. Removed .jugadores from partidos relations as it's not directly used here and can be heavy.

        // Calculate standings
        $standings = $campeonato->getStandings();
        
        // Get top scorers
        $goleadores = $this->getGoleadores($campeonato);

        // Get Fair Play data
        $fairPlay = $this->getFairPlay($campeonato);

        // Get Sancionados data
        $sancionadosData = $this->getSancionadosData($campeonato);

        // New progress bar logic based on matches
        $partidos = $campeonato->partidos->sortBy('fecha_partido');
        $totalPartidos = $partidos->count();
        $partidosFinalizados = $partidos->where('estado', 'finalizado')->count();
        $progressPercentage = $totalPartidos > 0 ? ($partidosFinalizados / $totalPartidos) * 100 : 0;
        $fechaInicio = $partidos->first()->fecha_partido ?? null;
        $fechaFin = $partidos->last()->fecha_partido ?? null;
        $fases = $campeonato->fases->sortBy('orden');

        // Separate matches into upcoming and played, and group by jornada
        $partidosJugados = $partidos->where('estado', 'finalizado')->groupBy('jornada');
        $partidosProximos = $partidos->where('estado', '!=', 'finalizado')->groupBy('jornada');

        // --- Lógica para calcular restingTeamsByJornada en el método show ---
        $restingTeamsByJornada = [];
        $teams = $campeonato->equipos;
        $numTeams = $teams->count();

        if ($numTeams % 2 !== 0) { // Only if there's an odd number of teams, a team rests
            $maxJornada = $partidos->max('jornada');
            for ($jornada = 1; $jornada <= $maxJornada; $jornada++) {
                $playingTeamIdsInJornada = $partidos->where('jornada', $jornada)
                                                    ->pluck('equipo_local_id')
                                                    ->merge($partidos->where('jornada', $jornada)->pluck('equipo_visitante_id'))
                                                    ->filter() // Remove nulls if any
                                                    ->unique()
                                                    ->toArray();
                
                $allRealTeamIds = $teams->pluck('id')->toArray();
                $restingTeamIds = array_diff($allRealTeamIds, $playingTeamIdsInJornada);

                if (!empty($restingTeamIds)) {
                    $restingTeam = \App\Models\Equipo::find(reset($restingTeamIds));
                    if ($restingTeam) {
                        $restingTeamsByJornada[$jornada] = $restingTeam->nombre;
                    }
                }
            }
        }
        // --- Fin de la lógica para restingTeamsByJornada ---

        $matchPairs = [];
        $duplicateMatchIds = [];
        foreach ($campeonato->partidos->sortBy('jornada') as $partido) {
            // Sort team IDs to make the pair order-independent
            $pair = collect([$partido->equipo_local_id, $partido->equipo_visitante_id])->sort()->values()->all();
            $pairKey = implode('-', $pair);

            if (isset($matchPairs[$pairKey])) {
                // This is a duplicate
                $duplicateMatchIds[] = $partido->id;
            } else {
                // First time seeing this pair
                $matchPairs[$pairKey] = $partido->id;
            }
        }

        return view('campeonatos.show', compact(
            'campeonato', 
            'sancionadosData', 
            'standings', 
            'fases', 
            'progressPercentage', 
            'totalPartidos',
            'partidosFinalizados',
            'fechaInicio',
            'fechaFin',
            'goleadores',
            'fairPlay',
            'partidosJugados',
            'partidosProximos',
            'restingTeamsByJornada', // Pasar la variable a la vista
            'duplicateMatchIds'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campeonato $campeonato)
    {
        return view('campeonatos.edit', compact('campeonato'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        $validatedData = $request->validate([
            'nombre_torneo' => 'required|string|max:255',
            'imagen_campeonato' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8192', // Aumentado a 8MB
            'equipos_max' => 'required|integer|min:2',
            'jugadores_por_equipo_max' => 'required|integer|min:1',
            'tipo_futbol' => 'required|in:5,7,11',
            'ubicacion_tipo' => 'required|in:unica,equipo_local',
            'cancha_unica_direccion' => 'nullable|string|max:255',
            'privacidad' => 'required|in:publico,privado',
            'reglamento_tipo' => 'nullable|in:pdf,texto',
            'reglamento_pdf' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'reglamento_texto' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('imagen_campeonato')) {
            // Delete old image if exists
            if ($campeonato->imagen_path) {
                Storage::disk('public')->delete($campeonato->imagen_path);
            }
            $validatedData['imagen_path'] = $request->file('imagen_campeonato')->store('campeonatos', 'public');
        }

        // Handle PDF upload
        if ($request->hasFile('reglamento_pdf')) {
            // Delete old PDF if exists
            if ($campeonato->reglamento_path) {
                Storage::delete($campeonato->reglamento_path); // Uncomment if using Laravel Storage
            }
            $validatedData['reglamento_path'] = $request->file('reglamento_pdf')->store('reglamentos', 'public'); // Store in 'storage/app/public/reglamentos'
        } elseif ($request->input('reglamento_tipo') !== 'pdf') {
            // If reglamento_tipo is not pdf, and no new pdf is uploaded, clear old pdf path
            if ($campeonato->reglamento_path) {
                Storage::delete($campeonato->reglamento_path); // Uncomment if using Laravel Storage
                $validatedData['reglamento_path'] = null;
            }
        }

        // Clear reglamento_texto if reglamento_tipo is pdf
        if ($request->input('reglamento_tipo') === 'pdf') {
            $validatedData['reglamento_texto'] = null;
        }

        // Clear reglamento_path if reglamento_tipo is texto
        if ($request->input('reglamento_tipo') === 'texto') {
            $validatedData['reglamento_path'] = null;
        }

        $campeonato->update($validatedData);

        return Redirect::route('campeonatos.index')->with('success', 'Campeonato actualizado con éxito.');
    }

    public function updateImage(Request $request, Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        $request->validate([
            'imagen_campeonato' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192', // Aumentado a 8MB
        ]);

        if ($request->hasFile('imagen_campeonato')) {
            // Delete old image if exists
            if ($campeonato->imagen_path) {
                Storage::disk('public')->delete($campeonato->imagen_path);
            }
            
            $path = $request->file('imagen_campeonato')->store('campeonatos', 'public');
            
            $campeonato->imagen_path = $path;
            $campeonato->save();
        }

        return Redirect::route('campeonatos.show', $campeonato)->with('success', 'Imagen del campeonato actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        SystemLog::add('campeonato.deleted', "El usuario {" . Auth::user()->name . "} eliminó el campeonato '{$campeonato->nombre_torneo}'", $campeonato);

        $campeonato->delete();

        return Redirect::route('campeonatos.index')->with('success', 'Campeonato eliminado con éxito.');
    }

    /**
     * Show the form for creating a new delegate.
     */
    public function createDelegateForm(Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);
        return view('campeonatos.delegates.create', compact('campeonato'));
    }

    /**
     * Store a new delegate for the championship.
     */
    public function storeDelegate(Request $request, Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        // Check if the maximum number of delegates has been reached
        $campeonato->loadCount('delegates');
        if ($campeonato->delegates_count >= $campeonato->equipos_max) {
            return Redirect::to(route('campeonatos.show', $campeonato) . '#delegates-section')->with('error', 'El campeonato ha alcanzado el número máximo de delegados permitidos (' . $campeonato->equipos_max . ').');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'dni' => ['required', 'string', 'max:20', Rule::unique('users')],
        ]);

        $delegate = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'dni' => $request->dni,
            'password' => Hash::make($request->dni), // Set initial password to DNI
            'role' => 'delegado',
        ]);

        $campeonato->delegates()->attach($delegate->id);

        return Redirect::to(route('campeonatos.show', $campeonato) . '#delegates-section')->with('success', 'Delegado añadido con éxito.');
    }

    /**
     * Store a new delegate and their team for the championship.
     */
    public function storeDelegateAndTeam(Request $request, Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        // Check if the maximum number of teams has been reached
        $campeonato->loadCount('equipos');
        if ($campeonato->equipos_count >= $campeonato->equipos_max) {
            return Redirect::to(route('campeonatos.show', $campeonato) . '#delegates-section')
                         ->with('error', 'El campeonato ha alcanzado el número máximo de equipos permitidos (' . $campeonato->equipos_max . ').');
        }

        $validatedData = $request->validate([
            'delegate_name' => ['required', 'string', 'max:255'],
            'delegate_email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'delegate_dni' => ['required', 'string', 'max:20', Rule::unique('users', 'dni')],
            'team_name' => ['required', 'string', 'max:255'],
            'team_description' => ['nullable', 'string'],
        ]);

        // Create the delegate user
        $delegate = User::create([
            'name' => $validatedData['delegate_name'],
            'email' => $validatedData['delegate_email'],
            'dni' => $validatedData['delegate_dni'],
            'password' => Hash::make($validatedData['delegate_dni']), // Set initial password to DNI
            'role' => 'delegado',
        ]);

        // Attach the delegate to the championship
        $campeonato->delegates()->attach($delegate->id);

        // Create the team
        $equipo = new Equipo([
            'nombre' => $validatedData['team_name'],
            'descripcion' => $validatedData['team_description'],
            'campeonato_id' => $campeonato->id,
            'user_id' => $delegate->id, // Associate team with the new delegate
        ]);
        $equipo->save();

        return Redirect::to(route('campeonatos.show', $campeonato) . '#delegates-section')
                     ->with('success', 'Delegado y equipo creados y asignados con éxito.');
    }

    /**
     * Remove the specified delegate from the championship and delete their user account,
     * along with their team and players if they have registered one.
     */
    public function destroyDelegate(Campeonato $campeonato, User $user)
    {
        $this->authorize('manage-campeonato', $campeonato);

        // Check if the delegate has a team registered in this championship
        $team = $campeonato->equipos()->where('user_id', $user->id)->first();

        if ($team) {
            // Delete all players associated with the team
            $team->jugadores()->delete();
            // Delete the team itself
            $team->delete();
        }

        // Detach the delegate from the championship
        $campeonato->delegates()->detach($user->id);

        // Delete the user account
        $user->delete();

        return Redirect::route('campeonatos.show', $campeonato)->with('success', 'Delegado y su equipo (si existía) eliminados con éxito.');
    }

    /**
     * Generate the match calendar for the specified championship.
     */
    public function generateCalendar(Request $request, Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        $request->validate([
            'tipo_torneo' => 'required|in:ida_vuelta,una_sola_ronda',
        ]);

        $teams = $campeonato->equipos; // Get all teams for the championship

        if ($teams->count() < 2) {
            return Redirect::back()->with('error', 'Se necesitan al menos 2 equipos para generar el calendario.');
        }

        // Clear existing matches for this championship to avoid duplicates
        $campeonato->partidos()->delete();

        $matches = [];
        $restingTeamsByJornada = [];
        $teamIds = $teams->pluck('id')->toArray();
        $originalNumTeams = $teams->count();

        // Round-robin algorithm (Circle Method)
        $hasDummyTeam = false;
        if ($originalNumTeams % 2 !== 0) {
            $teamIds[] = null; // Add a dummy team for odd number of teams
            $hasDummyTeam = true;
        }
        $numTeams = count($teamIds); // Update numTeams after potentially adding dummy

        $rounds = $numTeams - 1;
        $startDate = now()->startOfDay(); // Start from today or a configurable date

        for ($round = 0; $round < $rounds; $round++) {
            $currentJornadaDate = $startDate->copy()->addDays($round * 7);
            $currentJornada = $round + 1;

            for ($i = 0; $i < $numTeams / 2; $i++) {
                $team1 = $teamIds[$i];
                $team2 = $teamIds[$numTeams - 1 - $i];

                if ($team1 === null || $team2 === null) {
                    // One of the teams is the dummy team, so the other team rests
                    $restingTeamId = ($team1 === null) ? $team2 : $team1;
                    if ($restingTeamId !== null) {
                        $restingTeam = \App\Models\Equipo::find($restingTeamId);
                        if ($restingTeam) {
                            $restingTeamsByJornada[$currentJornada] = $restingTeam->nombre;
                        }
                    }
                } else {
                    // Both are real teams, create a match
                    $matches[] = [
                        'campeonato_id' => $campeonato->id,
                        'equipo_local_id' => $team1,
                        'equipo_visitante_id' => $team2,
                        'fecha_partido' => $currentJornadaDate,
                        'estado' => 'pendiente',
                        'jornada' => $currentJornada,
                        'ubicacion_partido' => $campeonato->ubicacion_tipo === 'unica' ? $campeonato->cancha_unica_direccion : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Double round (vuelta) if requested
                    if ($request->input('tipo_torneo') === 'ida_vuelta') {
                        $matches[] = [
                            'campeonato_id' => $campeonato->id,
                            'equipo_local_id' => $team2,
                            'equipo_visitante_id' => $team1,
                            'fecha_partido' => $startDate->copy()->addDays(($round + $rounds) * 7), // Schedule return leg after all first leg matches are done, maintaining weekly schedule
                            'estado' => 'pendiente',
                            'jornada' => $currentJornada + $rounds, // Adjust jornada for return leg
                            'ubicacion_partido' => $campeonato->ubicacion_tipo === 'unica' ? $campeonato->cancha_unica_direccion : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Rotate teams (keep first team fixed if it's a real team, otherwise rotate all)
            if ($hasDummyTeam && $teamIds[0] === null) {
                // If the dummy team is fixed, rotate the rest
                $tempTeams = array_slice($teamIds, 1);
                $lastOfTemp = array_pop($tempTeams);
                array_unshift($tempTeams, $lastOfTemp);
                $teamIds = array_values(array_merge([null], $tempTeams)); // Reconstruct with fixed dummy
            } else {
                // Standard rotation (keep first real team fixed)
                $tempTeams = array_slice($teamIds, 1);
                $lastOfTemp = array_pop($tempTeams);
                array_unshift($tempTeams, $lastOfTemp);
                $teamIds = array_values(array_merge([$teamIds[0]], $tempTeams));
            }
        }

        // Insert matches into the database
        \App\Models\Partido::insert($matches);

        return Redirect::route('campeonatos.show', $campeonato)->with('success', 'Calendario generado con éxito.')->with('restingTeamsByJornada', $restingTeamsByJornada);
    }

    /**
     * Store the result of a match and player statistics.
     */
    public function storeResult(Request $request, Partido $partido)
    {
        // Authorize that the user can manage the championship this match belongs to
        $this->authorize('manage-campeonato', $partido->campeonato);

        $validatedData = $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0',
            'jugadores' => 'array',
            'jugadores.*.goles' => 'nullable|integer|min:0',
            'jugadores.*.asistencias' => 'nullable|integer|min:0',
            'jugadores.*.amarillas' => 'nullable|integer|min:0',
            'jugadores.*.rojas' => 'nullable|integer|min:0',
        ]);

        // Update match scores and status
        $partido->goles_local = $validatedData['goles_local'];
        $partido->goles_visitante = $validatedData['goles_visitante'];
        $partido->estado = 'finalizado';
        $partido->save();

        // Serve suspensions for players who were suspended for this match
        $playersToUnsuspend = \App\Models\Jugador::where('suspended_until_match_id', $partido->id)->get();
        foreach ($playersToUnsuspend as $player) {
            $player->suspendido = false;
            $player->suspension_matches = 0;
            $player->suspended_until_match_id = null;
            $player->save();
        }

        // Update player statistics
        if (isset($validatedData['jugadores'])) {
            foreach ($validatedData['jugadores'] as $jugadorId => $stats) {
                $jugador = \App\Models\Jugador::find($jugadorId);
                if ($jugador) {
                    // Update overall player stats
                    $jugador->goles += ($stats['goles'] ?? 0);
                    $jugador->tarjetas_amarillas += ($stats['amarillas'] ?? 0);
                    $jugador->tarjetas_rojas += ($stats['rojas'] ?? 0);
                    $jugador->save();

                    // Save match-specific player statistics
                    \App\Models\PartidoJugadorEstadistica::updateOrCreate(
                        ['partido_id' => $partido->id, 'jugador_id' => $jugadorId],
                        [
                            'goles' => ($stats['goles'] ?? 0),
                            'asistencias' => ($stats['asistencias'] ?? 0),
                            'tarjetas_amarillas' => ($stats['amarillas'] ?? 0),
                            'tarjetas_rojas' => ($stats['rojas'] ?? 0),
                        ]
                    );

                    // Apply suspension logic
                    $nextMatch = $this->getNextMatchForTeam($partido->campeonato, $jugador->equipo, $partido);

                    if ($nextMatch) {
                        // Red card suspension
                        if (($stats['rojas'] ?? 0) > 0) {
                            $jugador->suspendido = true;
                            $jugador->suspended_until_match_id = $nextMatch->id;
                            $jugador->save();
                        } 
                        // Accumulated yellow cards suspension (every 2 yellow cards)
                        elseif (($stats['amarillas'] ?? 0) > 0 && $jugador->tarjetas_amarillas > 0 && ($jugador->tarjetas_amarillas % 2 === 0)) {
                            $jugador->suspendido = true;
                            $jugador->suspended_until_match_id = $nextMatch->id;
                            $jugador->save();
                        }
                    }
                }
            }
        }

        return Redirect::route('campeonatos.show', $partido->campeonato)->with('success', 'Resultado del partido guardado con éxito.') . '#calendario-partidos';
    }

    /**
     * Show the form for editing the specified match.
     */
    public function editMatch(Partido $partido)
    {
        $this->authorize('manage-campeonato', $partido->campeonato);

        $partido->load([
            'equipoLocal.jugadores',
            'equipoVisitante.jugadores',
            'estadisticasJugadores' // Load match-specific player statistics
        ]);

        // Prepare player statistics for easy access in the view
        $playerStats = $partido->estadisticasJugadores->keyBy('jugador_id');

        // Load all teams for the championship to populate dropdowns
        $teams = $partido->campeonato->equipos;

        // Get all matches for the championship to check for existing pairings
        $allMatches = $partido->campeonato->partidos;
        $existingPairings = [];
        foreach ($allMatches as $match) {
            // Exclude the current match from the check
            if ($match->id === $partido->id) {
                continue;
            }
            if ($match->equipo_local_id && $match->equipo_visitante_id) {
                $pair = collect([$match->equipo_local_id, $match->equipo_visitante_id])->sort()->values()->all();
                $existingPairings[$pair[0] . '-' . $pair[1]] = true;
            }
        }

        return view('partidos.edit', compact('partido', 'playerStats', 'teams', 'existingPairings'));
    }

    /**
     * Update the specified match in storage using a delta-based approach for stats.
     */
    public function updateMatch(Request $request, Partido $partido)
    {
        $this->authorize('manage-campeonato', $partido->campeonato);

        $validatedData = $request->validate([
            'fecha_partido' => 'required|date',
            'ubicacion_partido' => 'nullable|string|max:255',
            'estado' => 'required|in:pendiente,en_juego,finalizado,suspendido,reprogramado,cancelado',
            'goles_local' => 'nullable|integer|min:0',
            'goles_visitante' => 'nullable|integer|min:0',
            'equipo_local_id' => ['required', 'exists:equipos,id', Rule::notIn([$request->equipo_visitante_id])],
            'equipo_visitante_id' => ['required', 'exists:equipos,id', Rule::notIn([$request->equipo_local_id])],
            'jugadores' => 'array',
            'jugadores.*.goles' => 'nullable|integer|min:0',
            'jugadores.*.asistencias' => 'nullable|integer|min:0',
            'jugadores.*.amarillas' => 'nullable|integer|min:0|max:2',
            'jugadores.*.rojas' => 'nullable|integer|min:0|max:1',
        ]);

        // (The conflict check logic is good, I'll keep it)
        $fechaPartido = \Carbon\Carbon::parse($validatedData['fecha_partido'])->toDateString();
        $equipoLocalId = $validatedData['equipo_local_id'];
        $equipoVisitanteId = $validatedData['equipo_visitante_id'];

        $conflictingMatches = Partido::where('campeonato_id', $partido->campeonato_id)
            ->whereDate('fecha_partido', $fechaPartido)
            ->where('id', '!=', $partido->id)
            ->where(function ($query) use ($equipoLocalId, $equipoVisitanteId) {
                $query->where('equipo_local_id', $equipoLocalId)
                      ->orWhere('equipo_visitante_id', $equipoLocalId)
                      ->orWhere('equipo_local_id', $equipoVisitanteId)
                      ->orWhere('equipo_visitante_id', $equipoVisitanteId);
            })
            ->get();

        if ($conflictingMatches->isNotEmpty()) {
            $conflictingTeamIds = [];
            $inputTeams = [$equipoLocalId, $equipoVisitanteId];

            foreach ($inputTeams as $inputId) {
                $isConflicting = $conflictingMatches->some(function ($match) use ($inputId) {
                    return $match->equipo_local_id == $inputId || $match->equipo_visitante_id == $inputId;
                });

                if ($isConflicting) {
                    $conflictingTeamIds[] = $inputId;
                }
            }
            
            $conflictingTeamIds = array_unique($conflictingTeamIds);

            if (!empty($conflictingTeamIds)) {
                $teamNames = \App\Models\Equipo::whereIn('id', $conflictingTeamIds)->pluck('nombre')->implode(', ');
                $warningMessage = 'Advertencia: El/los equipo(s) (' . $teamNames . ') ya tienen un partido programado en esta fecha.';
                
                $request->session()->flash('warning', $warningMessage);
                $request->session()->flash('conflicting_teams', $conflictingTeamIds);
            }
        }

        DB::transaction(function () use ($partido, $validatedData, $request) {
            // 1. Get original stats before any changes
            $statsOriginales = $partido->estadisticasJugadores->keyBy('jugador_id');
            $jugadoresAfectados = $statsOriginales->keys();

            // 2. Update the match details itself
            $partido->update([
                'fecha_partido' => $validatedData['fecha_partido'],
                'ubicacion_partido' => $validatedData['ubicacion_partido'],
                'estado' => $validatedData['estado'],
                'goles_local' => $validatedData['goles_local'] ?? 0,
                'goles_visitante' => $validatedData['goles_visitante'] ?? 0,
                'equipo_local_id' => $validatedData['equipo_local_id'],
                'equipo_visitante_id' => $validatedData['equipo_visitante_id'],
            ]);

            // 3. Process player stats from the form
            $nuevosJugadoresStats = collect($validatedData['jugadores'] ?? []);
            $jugadoresAfectados = $jugadoresAfectados->merge($nuevosJugadoresStats->keys())->unique();

            foreach ($jugadoresAfectados as $jugadorId) {
                $jugador = \App\Models\Jugador::find($jugadorId);
                if (!$jugador) continue;

                $statsOriginal = $statsOriginales->get($jugadorId);
                $statsNuevas = $nuevosJugadoresStats->get($jugadorId) ?? [
                    'goles' => 0, 'asistencias' => 0, 'amarillas' => 0, 'rojas' => 0
                ];

                // Calculate deltas (difference between new and old stats for THIS match)
                $golesDelta = ($statsNuevas['goles'] ?? 0) - ($statsOriginal->goles ?? 0);
                // Assuming 'asistencias' column exists on Jugador model. If not, this line should be removed.
                // $asistenciasDelta = ($statsNuevas['asistencias'] ?? 0) - ($statsOriginal->asistencias ?? 0);
                $amarillasDelta = ($statsNuevas['amarillas'] ?? 0) - ($statsOriginal->tarjetas_amarillas ?? 0);
                $rojasDelta = ($statsNuevas['rojas'] ?? 0) - ($statsOriginal->tarjetas_rojas ?? 0);

                // Update match-specific stats
                \App\Models\PartidoJugadorEstadistica::updateOrCreate(
                    ['partido_id' => $partido->id, 'jugador_id' => $jugadorId],
                    [
                        'goles' => $statsNuevas['goles'] ?? 0,
                        'asistencias' => $statsNuevas['asistencias'] ?? 0,
                        'tarjetas_amarillas' => $statsNuevas['amarillas'] ?? 0,
                        'tarjetas_rojas' => $statsNuevas['rojas'] ?? 0,
                    ]
                );

                // Apply deltas to the player's total stats
                $jugador->goles = max(0, $jugador->goles + $golesDelta);
                // $jugador->asistencias += $asistenciasDelta;
                $jugador->tarjetas_amarillas = max(0, $jugador->tarjetas_amarillas + $amarillasDelta);
                $jugador->tarjetas_rojas = max(0, $jugador->tarjetas_rojas + $rojasDelta);

                // Reset suspension status before re-evaluating
                $jugador->suspendido = false;
                $jugador->suspended_until_match_id = null;

                // Apply suspension logic based on the NEW totals
                $nextMatch = $this->getNextMatchForTeam($partido->campeonato, $jugador->equipo, $partido);

                if ($nextMatch) {
                    if ($jugador->tarjetas_rojas > 0) {
                        $jugador->suspendido = true;
                        $jugador->suspended_until_match_id = $nextMatch->id;
                    } elseif ($jugador->tarjetas_amarillas > 0 && ($jugador->tarjetas_amarillas % 2 === 0)) {
                        $jugador->suspendido = true;
                        $jugador->suspended_until_match_id = $nextMatch->id;
                    }
                }
                
                $jugador->save();
            }
        });

        return redirect(route('campeonatos.show', $partido->campeonato) . '#partido-' . $partido->id)->with('success', 'Partido actualizado con éxito.');
    }

    /**
     * Remove the specified match from storage.
     */
    public function destroyMatch(Request $request, Partido $partido)
    {
        $this->authorize('manage-campeonato', $partido->campeonato);

        $partido->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Partido eliminado con éxito.']);
        }

        return Redirect::back()->with('success', 'Partido eliminado con éxito.');
    }

    /**
     * Get match-specific player statistics.
     */
    public function getMatchStatistics(Partido $partido)
    {
        $this->authorize('manage-campeonato', $partido->campeonato);

        $partido->load('estadisticasJugadores');

        return response()->json($partido->estadisticasJugadores->keyBy('jugador_id'));
    }

    /**
     * Get suspended players for a specific phase.
     */
    public function getSuspendedPlayersForPhase(Campeonato $campeonato, Fase $fase)
    {
        $this->authorize('manage-campeonato', $campeonato);

        $matchIdsInPhase = $fase->partidos->pluck('id');

        $suspendedPlayers = \App\Models\Jugador::whereIn('suspended_until_match_id', $matchIdsInPhase)
                                                ->with('equipo') // Eager load team to display team name
                                                ->get();

        return response()->json($suspendedPlayers);
    }

    /**
     * Resets the calendar for a specific championship by deleting all matches.
     */
    public function resetCalendar(Campeonato $campeonato)
    {
        $this->authorize('manage-campeonato', $campeonato);

        $campeonato->partidos()->delete();

        return Redirect::back()->with('success', 'El calendario del campeonato ha sido reiniciado exitosamente.');
    }

    public function toggleRegistrations(Campeonato $campeonato)
    {
        $this->authorize('update', $campeonato);

        $campeonato->registrations_open = !$campeonato->registrations_open;
        $campeonato->save();

        $status = $campeonato->registrations_open ? 'abierto' : 'cerrado';
        return back()->with('success', 'El estado de los registros se ha cambiado a: ' . $status);
    }

    public function getProgressData(Campeonato $campeonato)
    {
        // Load matches and phases with their matches
        $campeonato->load(['partidos' => function($query) {
            $query->orderBy('fecha_partido');
        }, 'fases.partidos']);

        // Overall progress
        $totalPartidos = $campeonato->partidos->count();
        $partidosFinalizados = $campeonato->partidos->where('estado', 'finalizado')->count();
        $progressPercentage = $totalPartidos > 0 ? round(($partidosFinalizados / $totalPartidos) * 100, 2) : 0;

        // Phase-specific progress
        $fasesData = $campeonato->fases->sortBy('orden')->map(function ($fase) {
            $totalPartidosFase = $fase->partidos->count();
            $partidosFinalizadosFase = $fase->partidos->where('estado', 'finalizado')->count();
            $progressPercentageFase = $totalPartidosFase > 0 ? round(($partidosFinalizadosFase / $totalPartidosFase) * 100, 2) : 0;

            return [
                'id' => $fase->id,
                'nombre' => $fase->nombre,
                'orden' => $fase->orden,
                'total_partidos' => $totalPartidosFase,
                'partidos_finalizados' => $partidosFinalizadosFase,
                'progress_percentage' => $progressPercentageFase,
            ];
        });

        return response()->json([
            'total_partidos_campeonato' => $totalPartidos,
            'partidos_finalizados_campeonato' => $partidosFinalizados,
            'progress_percentage_campeonato' => $progressPercentage,
            'fases' => $fasesData,
        ]);
    }

    /**
     * Get the next upcoming match for a given team in a championship, after a specific match.
     *
     * @param \App\Models\Campeonato $campeonato
     * @param \App\Models\Equipo $equipo
     * @param \App\Models\Partido $currentMatch
     * @return \App\Models\Partido|null
     */
    private function getNextMatchForTeam(Campeonato $campeonato, \App\Models\Equipo $equipo, Partido $currentMatch)
    {
        return Partido::where('campeonato_id', $campeonato->id)
            ->where(function ($query) use ($equipo) {
                $query->where('equipo_local_id', $equipo->id)
                      ->orWhere('equipo_visitante_id', $equipo->id);
            })
            ->where('fecha_partido', '>', $currentMatch->fecha_partido) // After the current match date
            ->where('id', '!=', $currentMatch->id) // Exclude the current match itself
            ->orderBy('fecha_partido')
            ->orderBy('id')
            ->first();
    }

    public function imprimirPadron(Campeonato $campeonato)
    {
        // Cargar las relaciones necesarias: equipos y jugadores de cada equipo.
        $campeonato->load('equipos.jugadores');

        // Crear el PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('campeonatos.pdf.padron', compact('campeonato'));
        $pdf->setPaper('a4', 'landscape'); // Hoja horizontal

        // Devolver el PDF para ser mostrado en el navegador
        return $pdf->stream('padron-de-jugadores-' . $campeonato->nombre_torneo . '.pdf');
    }
}
