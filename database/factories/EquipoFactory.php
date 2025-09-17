<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipo>
 */
class EquipoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campeonato_id' => \App\Models\Campeonato::factory(),
            'user_id' => \App\Models\User::factory(),
            'nombre' => $this->faker->company,
            'descripcion' => $this->faker->sentence,
            'cancha_direccion' => $this->faker->address,
        ];
    }
}
