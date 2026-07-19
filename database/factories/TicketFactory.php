<?php

namespace Database\Factories;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(TicketStatus::cases()),
            'priority' => $this->faker->randomElement(TicketPriority::cases()),
            'category' => $this->faker->randomElement(TicketCategory::cases()),
            'created_by' => User::role('Cliente')->inRandomOrder()->first()?->id ?? User::factory(),
            'assigned_to' => $this->faker->boolean(70) ? (User::role('Agente')->inRandomOrder()->first()?->id) : null,
            'team_id' => Team::inRandomOrder()->first()?->id,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'location' => $this->faker->randomElement(['Oficina 101', 'Piso 2', 'Data Center', 'Recepción', 'Lab 3']),
            'maintenance_type' => $this->faker->randomElement(['Hardware', 'Software', 'Red', 'Seguridad', 'Otro']),
        ];
    }
}
