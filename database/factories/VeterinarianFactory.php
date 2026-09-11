<?php

namespace Database\Factories;

use App\Models\Veterinarian;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeterinarianFactory extends Factory
{
    protected $model = Veterinarian::class;

    public function definition(): array
    {
        return [
            'cedula' => $this->faker->unique()->numerify('##########'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'specialty_id' => Specialty::inRandomOrder()->first()?->id ?? Specialty::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('09########'),
            'state' => 'active',
        ];
    }
}
