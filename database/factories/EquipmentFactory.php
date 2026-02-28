<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'name'          => $this->faker->words(3, true),
            'brand'         => $this->faker->randomElement(['HP', 'Dell', 'Lenovo', 'Asus', 'Apple']),
            'model'         => strtoupper($this->faker->bothify('???-###')),
            'serial_number' => strtoupper($this->faker->bothify('SN-########')),
            'type'          => $this->faker->randomElement(['Computadora', 'Impresora', 'Red', 'Servidor', 'Teléfono', 'Otro']),
        ];
    }
}
