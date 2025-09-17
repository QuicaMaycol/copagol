<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class JugadorController extends Controller
{
    /**
     * Show the form for creating a new player.
     */
    public function create(Equipo $equipo)
    {
        $this->authorize('update', $equipo);
        return view('jugadores.create', compact('equipo'));
    }

    /**
     * Store a newly created player in storage.
     */
    public function store(Request $request, Equipo $equipo)
    {
        $this->authorize('update', $equipo);

        // Load the championship to get the max players per team limit
        $campeonato = $equipo->campeonato;
        $maxJugadores = $campeonato->jugadores_por_equipo_max;

        // Check if adding a new player would exceed the limit
        if ($equipo->jugadores->count() >= $maxJugadores) {
            return Redirect::route('equipos.show', $equipo)->with('error', 'No se pueden agregar más jugadores. Se ha alcanzado el límite de ' . $maxJugadores . ' jugadores por equipo para este campeonato.');
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:jugadores,dni',
            'numero_camiseta' => 'nullable|integer|min:1',
            'posicion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'imagen_url' => 'nullable|url',
        ]);

        $jugador = new Jugador($validatedData);
        $equipo->jugadores()->save($jugador);

        return Redirect::route('equipos.show', $equipo)->with('success', 'Jugador registrado con éxito.');
    }

    /**
     * Display the specified player.
     */
    public function show(Equipo $equipo, Jugador $jugador)
    {
        // Ensure the player belongs to the team
        if ($jugador->equipo_id !== $equipo->id) {
            abort(404);
        }
        return view('jugadores.show', compact('equipo', 'jugador'));
    }

    /**
     * Show the form for editing the specified player.
     */
    public function edit(Equipo $equipo, Jugador $jugador)
    {
        $this->authorize('update', $equipo);

        // Ensure the player belongs to the team
        if ($jugador->equipo_id !== $equipo->id) {
            abort(404);
        }
        return view('jugadores.edit', compact('equipo', 'jugador'));
    }

    /**
     * Update the specified player in storage.
     */
    public function update(Request $request, Equipo $equipo, Jugador $jugador)
    {
        $this->authorize('update', $equipo);

        if ($jugador->equipo_id !== $equipo->id) {
            abort(404);
        }

        $campeonato = $equipo->campeonato;
        $user = $request->user();
        $isOrganizerOrAdmin = $user->id === $campeonato->user_id || $user->role === 'admin';

        $rules = [];

        // Define personal data rules only if registrations are open or the user is an admin/organizer
        if ($campeonato->registrations_open || $isOrganizerOrAdmin) {
            $rules = array_merge($rules, [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|max:20|unique:jugadores,dni,' . $jugador->id,
                'fecha_nacimiento' => 'required|date',
            ]);
        }

        // These fields can always be edited by an authorized user (delegates included)
        $rules = array_merge($rules, [
            'numero_camiseta' => 'nullable|integer|min:1',
            'posicion' => 'nullable|string|max:255',
            'imagen_url' => 'nullable|url',
        ]);

        $updateData = $request->validate($rules);

        // Capture original card values before update for suspension logic
        $originalTarjetasRojas = $jugador->tarjetas_rojas;
        $originalTarjetasAmarillas = $jugador->tarjetas_amarillas;

        // Validate and add stats data only for organizer/admin
        if ($isOrganizerOrAdmin) {
            $organizerData = $request->validate([
                'goles' => 'required|integer|min:0',
                'tarjetas_amarillas' => 'required|integer|min:0',
                'tarjetas_rojas' => 'required|integer|min:0',
                'suspendido' => 'boolean',
                'valoracion_general' => 'required|integer|min:1|max:100',
            ]);
            $updateData = array_merge($updateData, $organizerData);
        }

        // Laravel treats an unchecked checkbox as a non-present value.
        // We need to ensure 'suspendido' is set to false if it's not in the request and the user is an admin/organizer.
        if ($isOrganizerOrAdmin && !$request->has('suspendido')) {
            $updateData['suspendido'] = false;
        }

        $jugador->update($updateData);

        // Suspension Logic (only if stats were potentially updated by an admin/organizer)
        $suspensionMessage = '';
        if ($isOrganizerOrAdmin) {
            // Find the next match for the team in the same championship
            $nextMatch = null;
            $equipo->load('partidosLocal', 'partidosVisitante');

            $teamMatches = $equipo->partidosLocal->merge($equipo->partidosVisitante);
            $nextMatch = $teamMatches->where('fecha_partido', '>', now())
                                     ->sortBy('fecha_partido')
                                     ->first();

            // Red Card Suspension
            if ($jugador->tarjetas_rojas > $originalTarjetasRojas && $jugador->tarjetas_rojas >= 1) {
                $jugador->suspendido = true;
                $jugador->suspension_matches = 1; // Suspend for 1 match
                if ($nextMatch) {
                    $jugador->suspended_until_match_id = $nextMatch->id;
                }
                $suspensionMessage = 'Jugador suspendido por tarjeta roja.';
            }
            // Yellow Card Accumulation Suspension (e.g., 2 yellow cards)
            elseif ($jugador->tarjetas_amarillas >= 2 && $jugador->suspension_matches == 0) { // Only suspend if not already suspended
                $jugador->suspendido = true;
                $jugador->suspension_matches = 1; // Suspend for 1 match
                $jugador->tarjetas_amarillas = 0; // Reset yellow cards after suspension
                if ($nextMatch) {
                    $jugador->suspended_until_match_id = $nextMatch->id;
                }
                $suspensionMessage = 'Jugador suspendido por acumulación de tarjetas amarillas. Tarjetas amarillas reiniciadas.';
            }

            // Save suspension status if changed
            if ($suspensionMessage !== '') {
                $jugador->save();
            }
        }

        return Redirect::route('equipos.show', $equipo)->with('success', 'Jugador actualizado con éxito.' . ($suspensionMessage ? ' ' . $suspensionMessage : ''));
    }

    /**
     * Remove the specified player from storage.
     */
    public function destroy(Equipo $equipo, Jugador $jugador)
    {
        $this->authorize('update', $equipo);
        // Ensure the player belongs to the team
        if ($jugador->equipo_id !== $equipo->id) {
            abort(404);
        }

        $jugador->delete();

        return Redirect::route('equipos.show', $equipo)->with('success', 'Jugador eliminado con éxito.');
    }

    /**
     * Decrement suspension matches and lift suspension if completed.
     * This method should be called after a match is played.
     */
    public function serveSuspension(Jugador $jugador)
    {
        if ($jugador->suspendido && $jugador->suspension_matches > 0) {
            $jugador->suspension_matches--;
            if ($jugador->suspension_matches <= 0) {
                $jugador->suspendido = false;
                $jugador->suspension_matches = 0; // Ensure it's 0
            }
            $jugador->save();
        }
    }
}
