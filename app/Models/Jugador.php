<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Jugador extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'jugadores';

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'numero_camiseta',
        'posicion',
        'imagen_url',
        'equipo_id',
        'goles',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'suspendido',
        'valoracion_general',
        'fecha_nacimiento',
        'suspended_until_match_id',
        // 🔴 ALERTA: Los siguientes campos están pendientes de ser integrados completamente en el modelo y controlador.
        // Se están generando en el factory, pero no están en la migración ni en la lógica del controlador.
        // 'email',
        // 'celular',
        // 'visibilidad_fichaje',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    public function estadisticas()
    {
        return $this->hasMany(PartidoJugadorEstadistica::class);
    }
}
