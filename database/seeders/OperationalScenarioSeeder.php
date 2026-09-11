<?php

namespace Database\Seeders;

use App\Models\OperatingRoom;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\SchedulingConflict;
use App\Models\Specialty;
use App\Models\Species;
use App\Models\Surgery;
use App\Models\SurgeryType;
use App\Models\Veterinarian;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Crea un escenario operativo reproducible para revisar los indicadores.
 *
 * Los datos son plausibles, pero no constituyen historial real de la clínica.
 * La marca técnica `scenario` permite regenerarlos sin duplicar información.
 */
class OperationalScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->removePreviousScenario();

        $species = $this->catalogSpecies();
        $specialty = Specialty::query()->firstOrCreate(
            ['name' => 'Cirugía general'],
            ['description' => 'Procedimientos quirúrgicos veterinarios', 'state' => 'active']
        );
        $surgeryTypes = $this->catalogSurgeryTypes();
        $rooms = $this->rooms();
        $veterinarians = $this->veterinarians($specialty);
        $pets = $this->pets($species);

        $this->createSurgeries($surgeryTypes, $rooms, $veterinarians, $pets);
        $this->createConflictHistory($rooms, $veterinarians);
    }

    private function removePreviousScenario(): void
    {
        $scenarioPetIds = Pet::query()
            ->whereHas('owner', fn ($query) => $query->where('email', 'registro.operativo@clinica.local'))
            ->pluck('id');

        // El seeder debe poder reconstruir el escenario sin dejar cirugías
        // eliminadas lógicamente que interfieran con una nueva demostración.
        Surgery::query()->whereIn('pet_id', $scenarioPetIds)->forceDelete();
        SchedulingConflict::query()->whereIn('source', ['demo', 'scenario'])->delete();
    }

    private function catalogSpecies()
    {
        return collect([
            Species::query()->firstOrCreate(['name' => 'Canina'], ['description' => 'Perros', 'state' => 'active']),
            Species::query()->firstOrCreate(['name' => 'Felina'], ['description' => 'Gatos', 'state' => 'active']),
        ]);
    }

    private function catalogSurgeryTypes()
    {
        return collect([
            SurgeryType::query()->firstOrCreate(['name' => 'Esterilización'], ['description' => 'Procedimiento de esterilización', 'estimated_duration' => 60, 'state' => 'active']),
            SurgeryType::query()->firstOrCreate(['name' => 'Profilaxis dental'], ['description' => 'Limpieza y evaluación dental', 'estimated_duration' => 90, 'state' => 'active']),
            SurgeryType::query()->firstOrCreate(['name' => 'Sutura de heridas'], ['description' => 'Atención quirúrgica de heridas', 'estimated_duration' => 45, 'state' => 'active']),
        ]);
    }

    private function rooms()
    {
        return collect([
            OperatingRoom::query()->firstOrCreate(['name' => 'Quirófano A'], ['type' => 'General', 'state' => 'available']),
            OperatingRoom::query()->firstOrCreate(['name' => 'Quirófano B'], ['type' => 'General', 'state' => 'available']),
        ]);
    }

    private function veterinarians(Specialty $specialty)
    {
        return collect([
            Veterinarian::query()->firstOrCreate(
                ['email' => 'andrea.sanchez@clinica.local'],
                ['cedula' => '1701234561', 'first_name' => 'Andrea', 'last_name' => 'Sánchez', 'phone' => '0991112233', 'state' => 'active', 'specialty_id' => $specialty->id]
            ),
            Veterinarian::query()->firstOrCreate(
                ['email' => 'marco.torres@clinica.local'],
                ['cedula' => '1701234562', 'first_name' => 'Marco', 'last_name' => 'Torres', 'phone' => '0992223344', 'state' => 'active', 'specialty_id' => $specialty->id]
            ),
            Veterinarian::query()->firstOrCreate(
                ['email' => 'sofia.mendoza@clinica.local'],
                ['cedula' => '1701234563', 'first_name' => 'Sofía', 'last_name' => 'Mendoza', 'phone' => '0993334455', 'state' => 'active', 'specialty_id' => $specialty->id]
            ),
        ]);
    }

    private function pets($species)
    {
        $owner = Owner::query()->firstOrCreate(
            ['email' => 'registro.operativo@clinica.local'],
            ['cedula' => '1701234570', 'first_name' => 'María', 'last_name' => 'González', 'phone' => '0994567890', 'address' => 'Quito']
        );

        return collect([
            Pet::query()->firstOrCreate(['name' => 'Luna', 'owner_id' => $owner->id], ['species_id' => $species[0]->id, 'breed' => 'Mestiza', 'age' => 36, 'weight' => 12.50, 'gender' => 'female', 'state' => 'active']),
            Pet::query()->firstOrCreate(['name' => 'Max', 'owner_id' => $owner->id], ['species_id' => $species[0]->id, 'breed' => 'Labrador', 'age' => 48, 'weight' => 25.00, 'gender' => 'male', 'state' => 'active']),
            Pet::query()->firstOrCreate(['name' => 'Nala', 'owner_id' => $owner->id], ['species_id' => $species[1]->id, 'breed' => 'Criolla', 'age' => 24, 'weight' => 4.80, 'gender' => 'female', 'state' => 'active']),
            Pet::query()->firstOrCreate(['name' => 'Bruno', 'owner_id' => $owner->id], ['species_id' => $species[0]->id, 'breed' => 'Mestizo', 'age' => 60, 'weight' => 18.20, 'gender' => 'male', 'state' => 'active']),
        ]);
    }

    private function createSurgeries($surgeryTypes, $rooms, $veterinarians, $pets): void
    {
        $rows = [];
        $slots = ['08:00', '10:00', '14:00', '16:00'];

        foreach (range(1, 8) as $day) {
            foreach ([0, 1] as $slotIndex) {
                $rows[] = [
                    'date' => now()->subMonth()->subDays($day),
                    'state' => count($rows) < 12 ? 'completed' : (count($rows) === 12 ? 'cancelled' : 'no_show'),
                    'slot' => $slots[$slotIndex],
                ];
            }
        }

        foreach (range(1, 10) as $day) {
            foreach ([0, 1] as $slotIndex) {
                $index = count($rows);
                $rows[] = [
                    'date' => now()->subDays($day),
                    'state' => $index < 28 ? 'completed' : ($index < 30 ? 'cancelled' : ($index < 32 ? 'no_show' : 'scheduled')),
                    'slot' => $slots[$slotIndex],
                ];
            }
        }

        foreach ($rows as $index => $row) {
            $start = Carbon::createFromFormat('H:i', $row['slot']);
            $scheduled = $row['date']->copy()->setTimeFrom($start);

            if ($row['state'] === 'scheduled') {
                $scheduled = now()->addDay()->setTimeFrom($start);
            }

            $duration = $surgeryTypes[$index % $surgeryTypes->count()]->estimated_duration ?: 60;
            $end = $start->copy()->addMinutes($duration);
            $actualStart = $row['state'] === 'completed' ? $start->copy()->addMinutes($index % 6) : null;
            $actualEnd = $actualStart?->copy()->addMinutes($duration);

            $surgery = Surgery::query()->create([
                'pet_id' => $pets[$index % $pets->count()]->id,
                'veterinarian_id' => $veterinarians[$index % $veterinarians->count()]->id,
                'operating_room_id' => $rooms[$index % $rooms->count()]->id,
                'surgery_type_id' => $surgeryTypes[$index % $surgeryTypes->count()]->id,
                'scheduled_date' => $scheduled->toDateString(),
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'actual_start_time' => $actualStart?->format('H:i:s'),
                'actual_end_time' => $actualEnd?->format('H:i:s'),
                'state' => $row['state'],
                'notes' => $row['state'] === 'completed' ? 'Procedimiento finalizado sin novedades.' : null,
            ]);

            $createdAt = $scheduled->copy()->subDays(2)->subHours($index % 5);
            $surgery->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();
        }
    }

    private function createConflictHistory($rooms, $veterinarians): void
    {
        foreach ([
            ['veterinarian', $veterinarians[0]->id, $rooms[0]->id, '09:00', '10:00'],
            ['operating_room', $veterinarians[1]->id, $rooms[0]->id, '09:30', '10:30'],
            ['veterinarian', $veterinarians[0]->id, $rooms[1]->id, '14:00', '15:00'],
        ] as [$type, $veterinarianId, $roomId, $start, $end]) {
            SchedulingConflict::query()->create([
                'scheduled_date' => now()->subDay()->toDateString(),
                'start_time' => $start,
                'end_time' => $end,
                'conflict_type' => $type,
                'source' => 'scenario',
                'veterinarian_id' => $veterinarianId,
                'operating_room_id' => $roomId,
                'details' => ['message' => 'Intento rechazado por disponibilidad ocupada.'],
            ]);
        }
    }
}
