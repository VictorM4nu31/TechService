<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Equipo de Soporte N1', 
                'Infraestructura', 
                'Seguridad Informática', 
                'Desarrollo de Sistemas', 
                'Telecomunicaciones',
                'Mantenimiento Físico'
            ]),
            'description' => $this->faker->sentence(),
            'leader_id' => User::role('Agente')->inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
