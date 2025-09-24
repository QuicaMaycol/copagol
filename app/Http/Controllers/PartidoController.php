<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Equipo;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    

    /**
     * Genera la lista de jugadores suspendidos para un campeonato específico.
     *
     * @param  \App\Models\Campeonato  $campeonato
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function generarListaSuspendidos(Campeonato $campeonato)
    {
        return Jugador::where('suspendido', true)
            ->whereHas('equipo', function ($query) use ($campeonato) {
                $query->where('campeonato_id', $campeonato->id);
            })
            ->get();
    }

    /**
     * Actualiza el estado de sanción de los jugadores después de un partido.
     * Nota: Esta implementación asume que se desuspenden todos los jugadores
     * que estaban suspendidos. Para una lógica más granular (ej. suspensión por N partidos),
     * se necesitaría un campo adicional como 'suspended_until_match_id' o 'partidos_suspension_restantes'.
     *
     * @param  \App\Models\Partido  $partido (Opcional, para futura expansión)
     * @return \Illuminate\Http\Response
     */
    public function actualizarSancionesPostPartido(Partido $partido = null)
    {
        // Desuspender a todos los jugadores que estaban suspendidos.
        // Si se necesita una lógica más compleja (ej. solo los suspendidos para ESTE partido),
        // se debería pasar una lista específica de jugadores o usar el campo suspended_until_match_id.
        Jugador::where('suspendido', true)->update([
            'suspendido' => false,
            'tipo_sancion' => null,
            'tarjetas_amarillas' => 0, // Opcional: resetear amarillas si no se hizo al suspender
        ]);

        return back()->with('success', 'Sanciones actualizadas post-partido.');
    }

    /**
     * Obtiene los jugadores sancionados y con tarjetas amarillas para un partido específico.
     *
     * @param  \App\Models\Partido  $partido
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSancionados(Partido $partido)
    {
        $partido->load('equipoLocal.jugadores', 'equipoVisitante.jugadores');

        $equipoLocalId = $partido->equipoLocal->id;
        $equipoVisitanteId = $partido->equipoVisitante->id;

        $jugadores = Jugador::whereIn('equipo_id', [$equipoLocalId, $equipoVisitanteId])
                            ->with('equipo:id,nombre') // Cargar el nombre del equipo
                            ->get();

        $sancionados = $jugadores->filter(function ($jugador) {
            // Lógica de suspensión: jugador está marcado como suspendido.
            // En un futuro, se podría usar `partidos_sancion_restantes` o `suspended_until_match_id`.
            return $jugador->suspendido == true;
        })->map(function ($jugador) {
            return [
                'nombre' => $jugador->nombre . ' ' . $jugador->apellido,
                'equipo' => $jugador->equipo->nombre,
                'tipo_sancion' => $jugador->tipo_sancion ?? 'Roja Directa',
            ];
        });

        $conAmarilla = $jugadores->filter(function ($jugador) {
            // Jugadores con amarillas que NO están suspendidos (para no duplicar)
            return $jugador->tarjetas_amarillas > 0 && $jugador->suspendido == false;
        })->map(function ($jugador) {
            return [
                'nombre' => $jugador->nombre . ' ' . $jugador->apellido,
                'equipo' => $jugador->equipo->nombre,
                'cantidad' => $jugador->tarjetas_amarillas,
            ];
        });

        return response()->json([
            'sancionados' => $sancionados->values(),
            'conAmarilla' => $conAmarilla->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Campeonato $campeonato)
    {
        $equipos = $campeonato->equipos;
        return view('partidos.create', compact('campeonato', 'equipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Campeonato $campeonato)
    {
        $request->validate([
            'equipo_local_id' => 'required|exists:equipos,id',
            'equipo_visitante_id' => 'required|exists:equipos,id|different:equipo_local_id',
            'fecha_partido' => 'required|date',
            'jornada' => 'required|integer|min:1',
        ]);

        $partido = new Partido($request->all());
        $partido->campeonato_id = $campeonato->id;
        $partido->save();

        return redirect()->route('campeonatos.show', $campeonato)->with('success', 'Partido creado exitosamente.');
    }

    /**
     * Display the public details of a specific match.
     *
     * @param  \App\Models\Partido  $partido
     * @return \Illuminate\View\View
     */
    public function publicShow(Partido $partido)
    {
        $partido->load('equipoLocal.jugadores', 'equipoVisitante.jugadores', 'campeonato');

        return view('partidos.public_show', compact('partido'));
    }

    /**
     * Obtiene los oponentes para un equipo en un campeonato, indicando si ya han jugado.
     *
     * @param  \App\Models\Campeonato  $campeonato
     * @param  \App\Models\Equipo  $equipo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOponentes(Campeonato $campeonato, Equipo $equipo)
    {
        // Obtener todos los equipos del campeonato excepto el equipo local.
        $todosLosOponentes = $campeonato->equipos()->where('id', '!=', $equipo->id)->get();

        // Obtener los IDs de los equipos contra los que el equipo local ya ha jugado.
        $partidosJugados = Partido::where('campeonato_id', $campeonato->id)
            ->where(function ($query) use ($equipo) {
                $query->where('equipo_local_id', $equipo->id)
                      ->orWhere('equipo_visitante_id', $equipo->id);
            })
            ->get();

        $oponentesJugadosIds = $partidosJugados->map(function ($partido) use ($equipo) {
            return $partido->equipo_local_id == $equipo->id ? $partido->equipo_visitante_id : $partido->equipo_local_id;
        })->unique();

        // Formatear la respuesta.
        $oponentesConEstado = $todosLosOponentes->map(function ($oponente) use ($oponentesJugadosIds) {
            return [
                'id' => $oponente->id,
                'nombre' => $oponente->nombre,
                'jugado' => $oponentesJugadosIds->contains($oponente->id),
            ];
        });

        return response()->json($oponentesConEstado);
    }
}