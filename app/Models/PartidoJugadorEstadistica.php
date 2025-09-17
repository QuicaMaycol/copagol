<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidoJugadorEstadistica extends Model
{
    use HasFactory;

    protected $table = 'partido_jugador_estadisticas';

    protected $fillable = [
        'partido_id',
        'jugador_id',
        'goles',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas',
    ];

    /**
     * Get the match that the statistics belong to.
     */
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    /**
     * Get the player that the statistics belong to.
     */
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}
