<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketDraft>
 */
class TicketDraftFactory extends Factory
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
            'payload' => [
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(),
                'priority' => 'Media',
                'impact' => 'Medio',
                'urgency' => 'Medio',
                'category' => 'Correctivo',
                'maintenance_type' => 'Hardware',
            ],
            'saved_at' => now(),
        ];
    }
}
