<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jugador>
 */
class JugadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipo_id' => \App\Models\Equipo::factory(),
            'user_id' => null,
            'nombre' => $this->faker->firstName, // Changed to firstName for better separation
            'apellido' => $this->faker->lastName, // Added apellido field
            'dni' => $this->faker->unique()->numerify('########'),
            'email' => $this->faker->unique()->safeEmail,
            'celular' => $this->faker->phoneNumber,
            'visibilidad_fichaje' => $this->faker->boolean,

            // 🔴 ALERTA: Los campos de estadísticas y valoración se usarán en una mejora futura.
            // Por ahora, se generan con valores por defecto.
            'goles' => 0,
            'tarjetas_amarillas' => 0,
            'tarjetas_rojas' => 0,
            'suspendido' => false,
            'valoracion_general' => 50,
            'suspension_matches' => 0,
            'suspended_until' => null,
        ];
    }
}
