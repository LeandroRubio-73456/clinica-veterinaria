<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Owner;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'species_id' => Species::inRandomOrder()->first()?->id ?? Species::factory(),
            'owner_id' => Owner::inRandomOrder()->first()?->id ?? Owner::factory(),
            'breed' => $this->faker->randomElement(['Mestizo', 'Golden Retriever', 'Pastor Alemán', 'Siamés', 'Persa', 'Angora']),
            'age' => $this->faker->numberBetween(3, 180), // Edad expresada de 3 a 180 meses (hasta 15 años)
            'weight' => $this->faker->randomFloat(2, 1, 45), // Peso de 1kg a 45kg con 2 decimales
            'gender' => $this->faker->randomElement(['male', 'female']), // Enums en inglés
            'state' => 'active',
        ];
    }
}
