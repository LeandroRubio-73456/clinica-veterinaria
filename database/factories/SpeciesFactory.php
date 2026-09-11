<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpeciesFactory extends Factory
{
    protected $model = Species::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Canino', 'Felino', 'Ave', 'Roedor']),
            'description' => $this->faker->sentence(),
            'state' => 'active',
        ];
    }
}
