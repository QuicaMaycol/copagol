<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Partido extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'campeonato_id',
        'fase_id',
        'equipo_local_id',
        'equipo_visitante_id',
        'goles_local',
        'goles_visitante',
        'fecha_partido',
        'estado',
        'jornada',
        'ubicacion_partido',
    ];

    /**
     * Get the phase that the match belongs to.
     */
    public function fase()
    {
        return $this->belongsTo(Fase::class);
    }

    /**
     * Get the championship that the match belongs to.
     */
    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }

    /**
     * Get the home team for the match.
     */
    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    /**
     * Get the away team for the match.
     */
    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    /**
     * Get the player statistics for the match.
     */
    public function estadisticasJugadores()
    {
        return $this->hasMany(PartidoJugadorEstadistica::class);
    }

    /**
     * Determine if the local team is the winner.
     *
     * @return bool
     */
    public function getIsLocalWinnerAttribute()
    {
        return $this->estado === 'finalizado' && $this->goles_local > $this->goles_visitante;
    }

    /**
     * Determine if the visiting team is the winner.
     *
     * @return bool
     */
    public function getIsVisitanteWinnerAttribute()
    {
        return $this->estado === 'finalizado' && $this->goles_visitante > $this->goles_local;
    }
}
