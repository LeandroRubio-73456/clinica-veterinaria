<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Cirugía General', 'Anestesiología', 'Traumatología', 'Tejidos Blandos']),
            'description' => $this->faker->sentence(),
            'state' => 'active',
        ];
    }
}
