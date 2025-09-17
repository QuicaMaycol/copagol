<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Contracts\Auditable;

class Campeonato extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nombre_torneo',
        'imagen_url',
        'equipos_max',
        'jugadores_por_equipo_max',
        'tipo_futbol',
        'estado_torneo',
        'ubicacion_tipo',
        'cancha_unica_direccion',
        'privacidad',
        'reglamento_tipo',
        'reglamento_path',
        'reglamento_texto',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registrations_open' => 'boolean',
    ];

    /**
     * Get the user that owns the championship.
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the teams for the championship.
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    /**
     * The delegates that belong to the championship.
     */
    public function delegates()
    {
        return $this->belongsToMany(User::class, 'campeonato_user');
    }

    /**
     * Get the matches for the championship.
     */
    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }

    /**
     * Get the phases for the championship.
     */
    public function fases()
    {
        return $this->hasMany(Fase::class);
    }

    /**
     * Calculate and return the standings for the championship.
     */
    public function getStandings()
    {
        $standings = [];

        // Initialize standings for all teams
        foreach ($this->equipos as $equipo) {
            $standings[$equipo->id] = [
                'id' => $equipo->id,
                'nombre' => $equipo->nombre,
                'imagen_url' => $equipo->imagen_url, // Add imagen_url here
                'PJ' => 0, // Partidos Jugados
                'PG' => 0, // Partidos Ganados
                'PE' => 0, // Partidos Empatados
                'PP' => 0, // Partidos Perdidos
                'GF' => 0, // Goles a Favor
                'GC' => 0, // Goles en Contra
                'DG' => 0, // Diferencia de Goles
                'Pts' => 0, // Puntos
            ];
        }

        // Process finished matches
        $finishedMatches = $this->partidos->where('estado', 'finalizado');

        foreach ($finishedMatches as $partido) {
            $localId = $partido->equipo_local_id;
            $visitanteId = $partido->equipo_visitante_id;
            $golesLocal = $partido->goles_local;
            $golesVisitante = $partido->goles_visitante;

            if (!isset($standings[$localId]) || !isset($standings[$visitanteId])) {
                continue; // Skip if a team in the match is no longer in the championship
            }

            // Update stats for both teams
            $standings[$localId]['PJ']++;
            $standings[$visitanteId]['PJ']++;
            $standings[$localId]['GF'] += $golesLocal;
            $standings[$visitanteId]['GF'] += $golesVisitante;
            $standings[$localId]['GC'] += $golesVisitante;
            $standings[$visitanteId]['GC'] += $golesLocal;

            // Determine winner, loser, or draw
            if ($golesLocal > $golesVisitante) {
                $standings[$localId]['PG']++;
                $standings[$visitanteId]['PP']++;
                $standings[$localId]['Pts'] += 3;
            } elseif ($golesLocal < $golesVisitante) {
                $standings[$visitanteId]['PG']++;
                $standings[$localId]['PP']++;
                $standings[$visitanteId]['Pts'] += 3;
            } else {
                $standings[$localId]['PE']++;
                $standings[$visitanteId]['PE']++;
                $standings[$localId]['Pts'] += 1;
                $standings[$visitanteId]['Pts'] += 1;
            }
        }

        // Calculate goal difference and sort
        foreach ($standings as &$team) {
            $team['DG'] = $team['GF'] - $team['GC'];
        }

        // Sort the standings
        usort($standings, function ($a, $b) {
            if ($a['Pts'] !== $b['Pts']) {
                return $b['Pts'] <=> $a['Pts']; // Sort by Points descending
            }
            if ($a['DG'] !== $b['DG']) {
                return $b['DG'] <=> $a['DG']; // Sort by Goal Difference descending
            }
            if ($a['GF'] !== $b['GF']) {
                return $b['GF'] <=> $a['GF']; // Sort by Goals For descending
            }
            return $a['nombre'] <=> $b['nombre']; // Sort by name ascending as a tie-breaker
        });

        return $standings;
    }

    // ACCESSORS TO MATCH THE BLADE TEMPLATE

    /**
     * Accessor for id_campeonato.
     */
    public function getIdCampeonatoAttribute()
    {
        return $this->attributes['id'];
    }

    /**
     * Accessor for nombre_campeonato.
     */
    public function getNombreCampeonatoAttribute()
    {
        return $this->attributes['nombre_torneo'];
    }

    /**
     * Accessor for estado.
     */
    public function getEstadoAttribute()
    {
        return $this->attributes['estado_torneo'];
    }

    /**
     * Accessor for max_equipos.
     */
    public function getMaxEquiposAttribute()
    {
        return $this->attributes['equipos_max'];
    }

    /**
     * Accessor for ubicacion.
     */
    public function getUbicacionAttribute()
    {
        if ($this->attributes['ubicacion_tipo'] === 'unica') {
            return $this->attributes['cancha_unica_direccion'] ?? 'Ubicación única';
        }
        return 'Cancha propia de cada equipo';
    }
}
