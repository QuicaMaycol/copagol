<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Fase;
use App\Models\Equipo;
use App\Models\Partido;
use Illuminate\Http\Request;

class FaseController extends Controller
{
    /**
     * Show the form for creating a new phase.
     */
    public function create(Campeonato $campeonato)
    {
        return view('fases.create', compact('campeonato'));
    }

    /**
     * Store a newly created phase in storage.
     */
    public function store(Request $request, Campeonato $campeonato)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden' => 'required|integer|min:1',
            'tipo' => 'required|string|in:grupos,eliminatoria',
            'estado' => 'required|string|in:pendiente,activa,finalizada',
        ]);

        $fase = $campeonato->fases()->create($validated);

        return redirect()->route('campeonatos.fases.show', [$campeonato, $fase])
            ->with('success', 'Fase creada exitosamente. Ahora puedes crear los partidos de esta jornada.');
    }

    /**
     * Display the specified phase.
     */
    public function show(Campeonato $campeonato, Fase $fase)
    {
        // Ensure the phase belongs to the championship
        if ($fase->campeonato_id !== $campeonato->id) {
            abort(404);
        }

        // Eager load partidos with equipoLocal and equipoVisitante
        $fase->load('partidos.equipoLocal', 'partidos.equipoVisitante');

        return view('fases.show', compact('campeonato', 'fase'));
    }

    /**
     * Show the form for creating a new match for a phase.
     */
    public function createMatch(Campeonato $campeonato, Fase $fase)
    {
        // Ensure the phase belongs to the championship
        if ($fase->campeonato_id !== $campeonato->id) {
            abort(404);
        }

        $equipos = $campeonato->equipos; // Get all teams for the championship

        return view('fases.partidos.create', compact('campeonato', 'fase', 'equipos'));
    }

    /**
     * Store a newly created match in storage for a phase.
     */
    public function storeMatch(Request $request, Campeonato $campeonato, Fase $fase)
    {
        // Ensure the phase belongs to the championship
        if ($fase->campeonato_id !== $campeonato->id) {
            abort(404);
        }

        $validated = $request->validate([
            'equipo_local_id' => 'required|exists:equipos,id',
            'equipo_visitante_id' => 'required|exists:equipos,id|different:equipo_local_id',
            'fecha_partido' => 'required|date',
            'ubicacion_partido' => 'nullable|string|max:255',
        ]);

        $fase->partidos()->create([
            'campeonato_id' => $campeonato->id,
            'equipo_local_id' => $validated['equipo_local_id'],
            'equipo_visitante_id' => $validated['equipo_visitante_id'],
            'fecha_partido' => $validated['fecha_partido'],
            'ubicacion_partido' => $validated['ubicacion_partido'],
            'estado' => 'pendiente',
            'jornada' => 1, // Default to 1, can be updated later if needed
        ]);

        return redirect()->route('campeonatos.fases.show', [$campeonato, $fase])->with('success', 'Partido creado exitosamente.');
    }

    /**
     * Remove the specified phase from storage.
     */
    public function destroy(Campeonato $campeonato, Fase $fase)
    {
        // Ensure the phase belongs to the championship
        if ($fase->campeonato_id !== $campeonato->id) {
            abort(404);
        }

        // Delete all matches associated with this phase
        $fase->partidos()->delete();

        // Delete the phase itself
        $fase->delete();

        return redirect()->route('campeonatos.show', $campeonato)->with('success', 'Fase y todos sus partidos eliminados exitosamente.');
    }
}
