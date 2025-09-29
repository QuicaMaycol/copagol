<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $campeonato = Campeonato::findOrFail($request->query('campeonato'));
        // Authorize that the user can add a team. 
        // This is a bit tricky because we don't have an Equipo instance yet.
        // We will check if the user is a delegate or the championship owner.
        $user = Auth::user();
        $isDelegate = $campeonato->delegates->pluck('id')->contains($user->id);
        $isOwner = $campeonato->user_id === $user->id;

        if (!$isDelegate && !$isOwner) {
             abort(403, 'No tienes permiso para agregar un equipo a este campeonato.');
        }

        return view('equipos.create', compact('campeonato'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|url',
            'campeonato_id' => 'required|exists:campeonatos,id',
        ]);

        $campeonato = Campeonato::findOrFail($validatedData['campeonato_id']);
        $user = Auth::user();

        // Authorization: Check if user is a delegate for this championship
        $isDelegate = $campeonato->delegates->pluck('id')->contains($user->id);
        $isOwner = $campeonato->user_id === $user->id;

        if (!$isDelegate && !$isOwner) {
            return Redirect::route('campeonatos.show', $campeonato)->with('error', 'No tienes permiso para agregar un equipo a este campeonato.');
        }

        // Authorization: Check if delegate already has a team in this championship
        if ($isDelegate && $user->equipos()->where('campeonato_id', $campeonato->id)->exists()) {
            return Redirect::route('campeonatos.show', $campeonato)->with('error', 'Ya tienes un equipo registrado en este campeonato.');
        }

        // Check if the championship is full
        $campeonato->loadCount('equipos'); // Get the current count of teams
        if ($campeonato->equipos_count >= $campeonato->equipos_max) {
            return Redirect::route('campeonatos.show', $campeonato)->with('error', 'El campeonato ha alcanzado el número máximo de equipos.');
        }

        $equipo = new Equipo($validatedData);
        $equipo->user_id = $user->id; // The delegate is the owner of the team
        $equipo->save();

        Log::info('EquipoController@store: Checking championship status for transition.', [
            'campeonato_id' => $campeonato->id,
            'current_estado_torneo' => $campeonato->estado_torneo,
            'equipos_count_after_save' => $campeonato->equipos_count,
            'equipos_max' => $campeonato->equipos_max,
        ]);

        // Check if the championship should transition to 'en_curso'
        // $campeonato->loadCount('equipos'); // Reload the count of teams - already loaded above
        if ($campeonato->estado_torneo === 'inscripciones_abiertas' && $campeonato->equipos_count >= $campeonato->equipos_max) {
            $campeonato->estado_torneo = 'en_curso';
            $campeonato->save();
            Log::info('EquipoController@store: Championship status transitioned to en_curso.', [
                'campeonato_id' => $campeonato->id,
                'new_estado_torneo' => $campeonato->estado_torneo,
            ]);
        }

        return Redirect::route('equipos.show', $equipo)->with('success', 'Equipo registrado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipo $equipo)
    {
        // Load relationships if needed, e.g., players
        $equipo->load('jugadores', 'campeonato');

        // Determine if the current user can manage this team
        $canManage = false;
        if (Auth::check()) {
            $user = Auth::user();
            $canManage = $user->id === $equipo->user_id || $user->id === $equipo->campeonato->user_id;
        }

        // Placeholder data for matches and stats
        // In a real application, you would fetch this data from your database
        // based on the $equipo object.
        $jugadores = $equipo->jugadores;

        // Calculate top scorers within the team
        $goleadores = $jugadores->filter(function ($jugador) {
            return $jugador->goles > 0;
        })->sortByDesc('goles')->values()->map(function ($jugador, $key) {
            $jugador->goleador_rank = $key + 1;
            return $jugador;
        });

        // Filter players for cards and suspensions
        $jugadoresConRojas = $jugadores->filter(function ($jugador) {
            return $jugador->tarjetas_rojas > 0;
        });

        $jugadoresEnCapilla = $jugadores->filter(function ($jugador) {
            // Assuming 5 yellow cards lead to suspension, 4 yellow cards means 'en capilla'
            return $jugador->tarjetas_amarillas >= 4 && !$jugador->suspendido;
        });

        $jugadoresSuspendidos = $jugadores->filter(function ($jugador) {
            return $jugador->suspendido;
        });

        // Get last 2 matches
        $ultimosPartidos = \App\Models\Partido::where(function ($query) use ($equipo) {
            $query->where('equipo_local_id', $equipo->id)
                  ->orWhere('equipo_visitante_id', $equipo->id);
        })
        ->where('estado', 'finalizado')
        ->orderByDesc('fecha_partido')
        ->limit(2)
        ->get();

        // Get next match
        $proximoPartido = \App\Models\Partido::where(function ($query) use ($equipo) {
            $query->where('equipo_local_id', $equipo->id)
                  ->orWhere('equipo_visitante_id', $equipo->id);
        })
        ->where('estado', 'pendiente') // Assuming 'pendiente' is the status for upcoming matches
        ->orderBy('fecha_partido')
        ->first();

        return view('equipos.show', compact('equipo', 'jugadores', 'goleadores', 'jugadoresConRojas', 'jugadoresEnCapilla', 'jugadoresSuspendidos', 'ultimosPartidos', 'proximoPartido', 'canManage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipo $equipo)
    {
        $this->authorize('update', $equipo); // Assuming an 'update' policy for Equipo
        return view('equipos.edit', compact('equipo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipo $equipo)
    {
        $this->authorize('update', $equipo); // Assuming an 'update' policy for Equipo

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen_equipo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8192', // Aumentado a 8MB
        ]);

        // Handle image upload
        if ($request->hasFile('imagen_equipo')) {
            // Delete old image if exists
            if ($equipo->imagen_path) {
                Storage::disk('public')->delete($equipo->imagen_path);
            }
            $equipo->imagen_path = $request->file('imagen_equipo')->store('equipos', 'public');
        }

        // Update other fields if they are present in the request
        if ($request->has('nombre')) {
            $equipo->nombre = $validatedData['nombre'];
        }
        if ($request->has('descripcion')) {
            $equipo->descripcion = $validatedData['descripcion'];
        }
        
        $equipo->save();

        return Redirect::route('equipos.show', $equipo)->with('success', 'Equipo actualizado con éxito.');
    }

    public function updateImage(Request $request, Equipo $equipo)
    {
        $this->authorize('update', $equipo);

        $request->validate([
            'imagen_equipo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192', // Aumentado a 8MB
        ]);

        if ($request->hasFile('imagen_equipo')) {
            // Delete old image if exists
            if ($equipo->imagen_path) {
                Storage::disk('public')->delete($equipo->imagen_path);
            }
            
            $path = $request->file('imagen_equipo')->store('equipos', 'public');
            
            $equipo->imagen_path = $path;
            $equipo->save();
        }

        return Redirect::route('equipos.show', $equipo)->with('success', 'Imagen del equipo actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipo $equipo)
    {
        $this->authorize('update', $equipo); // Assuming an 'update' policy for Equipo

        $equipo->delete();

        return Redirect::route('campeonatos.show', $equipo->campeonato_id)->with('success', 'Equipo eliminado con éxito.');
    }

    public function publicShow(Equipo $equipo)
    {
        $equipo->load('jugadores', 'campeonato.organizador');

        $jugadores = $equipo->jugadores->map(function ($jugador) {
            $jugador->edad = \Carbon\Carbon::parse($jugador->fecha_nacimiento)->age;
            return $jugador;
        });

        $goleadores = $jugadores->where('goles', '>', 0)->sortByDesc('goles');

        return view('equipos.public_show', compact('equipo', 'jugadores', 'goleadores'));
    }
}
