<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceSchedule>
 */
class MaintenanceScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipment_id' => \App\Models\Equipment::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'frequency_days' => $this->faker->randomElement([30, 90, 180, 365]),
            'is_active' => true,
            'next_run_at' => now()->addDays($this->faker->numberBetween(1, 30)),
        ];
    }
}
