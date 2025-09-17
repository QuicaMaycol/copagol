<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campeonato>
 */
class CampeonatoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'nombre_torneo' => $this->faker->sentence(3),
            'equipos_max' => $this->faker->numberBetween(8, 32),
            'jugadores_por_equipo_max' => $this->faker->numberBetween(10, 20),
            'tipo_futbol' => $this->faker->randomElement(['5', '7', '11']),
            'estado_torneo' => $this->faker->randomElement(['inscripciones_abiertas', 'en_curso', 'finalizado', 'cancelado']),
            'ubicacion_tipo' => $this->faker->randomElement(['unica', 'equipo_local']),
            'cancha_unica_direccion' => $this->faker->address,
            'privacidad' => $this->faker->randomElement(['publico', 'privado']),
            'reglamento_tipo' => $this->faker->randomElement(['pdf', 'texto']),
            'reglamento_texto' => $this->faker->paragraph,
        ];
    }
}
