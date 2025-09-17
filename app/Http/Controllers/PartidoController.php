<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Jugador;
use App\Models\Partido;
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
}