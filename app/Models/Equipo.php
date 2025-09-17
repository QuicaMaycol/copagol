<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Equipo extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_url',
        'cancha_direccion',
        'campeonato_id',
        'user_id',
    ];

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class);
    }

    /**
     * Get the home matches for the team.
     */
    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    /**
     * Get the away matches for the team.
     */
    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }
}