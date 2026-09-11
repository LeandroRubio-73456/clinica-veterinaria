<?php

namespace Database\Factories;

use App\Models\SurgeryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurgeryTypeFactory extends Factory
{
    protected $model = SurgeryType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Esterilización', 'Castración', 'Profilaxis Dental', 'Ortopedia', 'Sutura de Heridas']),
            'description' => $this->faker->sentence(),
            'estimated_duration' => $this->faker->randomElement([30, 45, 60, 90, 120]), // Duración en minutos
            'state' => 'active',
        ];
    }
}
