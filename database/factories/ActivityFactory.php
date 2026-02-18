<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['created', 'assigned', 'commented', 'status_updated', 'resolved'];
        $type = $this->faker->randomElement($types);
        
        $descriptions = [
            'created' => 'creó el ticket',
            'assigned' => 'asignó el ticket a ' . $this->faker->name(),
            'commented' => 'comentó en el ticket',
            'status_updated' => 'cambió el estado del ticket',
            'resolved' => 'resolvió el ticket',
        ];

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'ticket_id' => Ticket::factory(),
            'type' => $type,
            'description' => $descriptions[$type],
            'properties' => null,
        ];
    }
}
