<?php

namespace Database\Factories;

use App\Models\Surgery;
use App\Models\Pet;
use App\Models\Veterinarian;
use App\Models\OperatingRoom;
use App\Models\SurgeryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition(): array
    {
        $scheduledDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $startTime = $this->faker->randomElement(['08:00:00', '09:30:00', '11:00:00', '14:00:00', '15:30:00']);
        
        // Calcular fin estimado sumando 1 hora por defecto a la simulación
        $endTime = date('H:i:s', strtotime($startTime) + 3600); 
        $state = $this->faker->randomElement(['scheduled', 'completed', 'cancelled', 'no_show']);

        return [
            'pet_id' => Pet::inRandomOrder()->first()?->id ?? Pet::factory(),
            'veterinarian_id' => Veterinarian::inRandomOrder()->first()?->id ?? Veterinarian::factory(),
            'operating_room_id' => OperatingRoom::inRandomOrder()->first()?->id ?? OperatingRoom::factory(),
            'surgery_type_id' => SurgeryType::inRandomOrder()->first()?->id ?? SurgeryType::factory(),'scheduled_date' => $scheduledDate->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'actual_start_time' => in_array($state, ['in_progress', 'completed']) ? $startTime : null,
            'actual_end_time' => ($state === 'completed') ? $endTime : null,
            'state' => $state,
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
