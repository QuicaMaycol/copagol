<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campeonato_id',
        'nombre',
        'orden',
        'tipo',
        'estado',
    ];

    /**
     * Get the championship that owns the phase.
     */
    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }

    /**
     * Get the matches for the phase.
     */
    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}