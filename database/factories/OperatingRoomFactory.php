<?php

namespace Database\Factories;

use App\Models\OperatingRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperatingRoomFactory extends Factory
{
    protected $model = OperatingRoom::class;

    public function definition(): array
    {
        return [
            'name' => 'Quirófano ' . $this->faker->unique()->randomElement(['A', 'B', 'C', 'Emergencias']),
            'type' => $this->faker->randomElement(['General', 'Especializado']),
            'state' => $this->faker->randomElement(['available', 'occupied', 'maintenance', 'inactive']),
        ];
    }
}
