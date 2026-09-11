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
use App\Models\Role;
use App\Models\User;
use App\Services\SurgeryEmailService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Escenario reproducible para la demostración de ejemplo.
 *
 * No se llama desde DatabaseSeeder: se ejecuta explícitamente antes de la demo.
 * Todos los registros propios se identifican con correos/source `example` para
 * poder reconstruir el escenario sin duplicarlo ni tocar datos ajenos.
 */
class ExampleScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->removePreviousScenario();

        $species = $this->catalogSpecies();
        $specialties = $this->catalogSpecialties();
        $types = $this->catalogSurgeryTypes();
        $rooms = $this->rooms();
        $veterinarians = $this->veterinarians($specialties);
        [$owners, $pets] = $this->ownersAndPets($species);
        $this->accessUsers();

        $surgeries = $this->surgeries($types, $rooms, $veterinarians, $pets);
        $this->conflicts($surgeries, $rooms, $veterinarians);
        $this->mailEvidence($surgeries);

        $this->command?->info('Escenario de demostracion creado: 29 cirugías, 12 propietarios y 18 mascotas.');
    }

    private function removePreviousScenario(): void
    {
        $ownerIds = Owner::withTrashed()
            ->where('email', 'like', 'ejemplo.propietario.%@clinica.local')
            ->pluck('id');
        $petIds = Pet::withTrashed()->whereIn('owner_id', $ownerIds)->pluck('id');

        Surgery::withTrashed()->whereIn('pet_id', $petIds)->forceDelete();
        Pet::withTrashed()->whereIn('id', $petIds)->forceDelete();
        Owner::withTrashed()->whereIn('id', $ownerIds)->forceDelete();
        User::query()->where('email', 'like', 'ejemplo.propietario.%@clinica.local')->delete();
        User::query()->whereIn('email', [
            'ejemplo.administrativo@clinica.local',
            'ejemplo.veterinario@clinica.local',
        ])->delete();
        SchedulingConflict::query()->where('source', 'example')->delete();
        DB::table('surgery_email_logs')->where('recipient_email', 'like', 'ejemplo.%@clinica.local')->delete();
    }

    private function accessUsers(): void
    {
        $roles = Role::query()->whereIn('slug', ['admin', 'administrativo', 'veterinario'])->pluck('id', 'slug');

        User::updateOrCreate(
            ['email' => 'admin@clinica.gob.ec'],
            ['name' => 'Rubio Leandro Admin', 'password' => 'password', 'role' => 'admin', 'role_id' => $roles['admin'] ?? null]
        );

        User::updateOrCreate(
            ['email' => 'ejemplo.administrativo@clinica.local'],
            ['name' => 'Usuario Administrativo', 'password' => 'password', 'role' => 'administrativo', 'role_id' => $roles['administrativo'] ?? null]
        );

        User::updateOrCreate(
            ['email' => 'ejemplo.veterinario@clinica.local'],
            ['name' => 'Usuario Veterinario', 'password' => 'password', 'role' => 'veterinario', 'role_id' => $roles['veterinario'] ?? null]
        );
    }

    private function catalogSpecies()
    {
        return collect([
            Species::updateOrCreate(['name' => 'Canina'], ['description' => 'Perros', 'state' => 'active']),
            Species::updateOrCreate(['name' => 'Felina'], ['description' => 'Gatos', 'state' => 'active']),
            Species::updateOrCreate(['name' => 'Avian'], ['description' => 'Aves domésticas', 'state' => 'active']),
        ]);
    }

    private function catalogSpecialties()
    {
        return collect([
            Specialty::updateOrCreate(['name' => 'Cirugía general'], ['description' => 'Procedimientos generales', 'state' => 'active']),
            Specialty::updateOrCreate(['name' => 'Anestesiología'], ['description' => 'Manejo anestésico', 'state' => 'active']),
            Specialty::updateOrCreate(['name' => 'Traumatología'], ['description' => 'Lesiones y ortopedia', 'state' => 'active']),
        ]);
    }

    private function catalogSurgeryTypes()
    {
        return collect([
            SurgeryType::updateOrCreate(['name' => 'Esterilización'], ['description' => 'Esterilización preventiva', 'estimated_duration' => 60, 'state' => 'active']),
            SurgeryType::updateOrCreate(['name' => 'Profilaxis dental'], ['description' => 'Limpieza y evaluación dental', 'estimated_duration' => 90, 'state' => 'active']),
            SurgeryType::updateOrCreate(['name' => 'Sutura de heridas'], ['description' => 'Atención de heridas', 'estimated_duration' => 45, 'state' => 'active']),
            SurgeryType::updateOrCreate(['name' => 'Corrección ortopédica'], ['description' => 'Procedimiento del sistema locomotor', 'estimated_duration' => 120, 'state' => 'active']),
        ]);
    }

    private function rooms()
    {
        return collect([
            OperatingRoom::updateOrCreate(['name' => 'Quirófano A'], ['type' => 'General', 'state' => 'available']),
            OperatingRoom::updateOrCreate(['name' => 'Quirófano B'], ['type' => 'General', 'state' => 'available']),
            OperatingRoom::updateOrCreate(['name' => 'Quirófano C'], ['type' => 'Especializado', 'state' => 'available']),
        ]);
    }

    private function veterinarians($specialties)
    {
        return collect([
            Veterinarian::updateOrCreate(['email' => 'laura.morales@clinica.local'], ['cedula' => '1702345671', 'first_name' => 'Laura', 'last_name' => 'Morales', 'phone' => '0994001001', 'state' => 'active', 'specialty_id' => $specialties[0]->id]),
            Veterinarian::updateOrCreate(['email' => 'diego.castro@clinica.local'], ['cedula' => '1702345672', 'first_name' => 'Diego', 'last_name' => 'Castro', 'phone' => '0994001002', 'state' => 'active', 'specialty_id' => $specialties[1]->id]),
            Veterinarian::updateOrCreate(['email' => 'camila.vega@clinica.local'], ['cedula' => '1702345673', 'first_name' => 'Camila', 'last_name' => 'Vega', 'phone' => '0994001003', 'state' => 'active', 'specialty_id' => $specialties[2]->id]),
        ]);
    }

    private function ownersAndPets($species): array
    {
        $names = [
            ['Ana', 'Paredes'], ['Luis', 'Mendoza'], ['Carolina', 'Ruiz'], ['Jorge', 'Cevallos'],
            ['Mónica', 'Viteri'], ['Daniel', 'Salazar'], ['Paola', 'Torres'], ['Andrés', 'Sánchez'],
            ['Verónica', 'López'], ['Gabriela', 'Naranjo'], ['Martín', 'Ponce'], ['Sofía', 'Herrera'],
        ];
        $petNames = ['Toby', 'Mia', 'Simba', 'Kira', 'Rocky', 'Lola', 'Coco', 'Dante', 'Maya', 'Bimba', 'Tom', 'Kiara', 'Chispa', 'Nina', 'Oso', 'Roma', ' Milo', 'Lía'];
        $owners = collect();
        $pets = collect();

        foreach ($names as $index => [$first, $last]) {
            $cedula = sprintf('17023457%02d', $index + 1);

            $owner = Owner::updateOrCreate(
                ['email' => sprintf('ejemplo.propietario.%02d@clinica.local', $index + 1)],
                ['cedula' => $cedula, 'first_name' => $first, 'last_name' => $last, 'phone' => sprintf('098500%04d', $index + 1), 'address' => 'Quito']
            );
            $owners->push($owner);

            $user = User::updateOrCreate(
                ['email' => $owner->email],
                ['name' => "{$first} {$last}", 'username' => $cedula, 'password' => 'password', 'role' => 'administrativo', 'role_id' => Role::where('slug', 'propietario')->value('id')]
            );
            $owner->update(['user_id' => $user->id]);

            for ($petNumber = 0; $petNumber < ($index < 6 ? 2 : 1); $petNumber++) {
                $petIndex = $owners->count() + $petNumber - 1;
                $pets->push(Pet::updateOrCreate(
                    ['name' => trim($petNames[$petIndex]), 'owner_id' => $owner->id],
                    ['species_id' => $species[$petIndex % $species->count()]->id, 'breed' => $petIndex % 2 ? 'Mestizo' : 'Criollo', 'age' => 18 + $petIndex * 3, 'weight' => 4.5 + $petIndex, 'gender' => $petIndex % 2 ? 'male' : 'female', 'state' => 'active']
                ));
            }
        }

        return [$owners, $pets];
    }

    private function surgeries($types, $rooms, $veterinarians, $pets)
    {
        $rows = [];
        foreach (range(0, 14) as $index) {
            $rows[] = ['state' => 'completed', 'date' => now()->subDays(2 + $index), 'slot' => ['08:00', '10:00', '14:00'][$index % 3], 'delay' => $index % 5 === 0 ? 15 : ($index % 3 === 0 ? 5 : 0)];
        }
        foreach (range(0, 3) as $index) {
            $rows[] = ['state' => 'cancelled', 'date' => now()->subDays(4 + $index), 'slot' => ['09:00', '11:00', '13:00', '15:00'][$index], 'notes' => ['Propietario solicitó reprogramación por viaje.', 'Mascota presentó fiebre en la evaluación previa.', 'No se completaron los exámenes preoperatorios.', 'Quirófano reservado para una emergencia.'][$index]];
        }
        foreach (range(0, 2) as $index) {
            $rows[] = ['state' => 'no_show', 'date' => now()->subMinutes(45 + ($index * 30)), 'slot' => ['08:00', '10:00', '14:00'][$index], 'notes' => 'El propietario no se presentó después de la tolerancia establecida.'];
        }
        foreach (range(0, 1) as $index) {
            $rows[] = ['state' => 'in_progress', 'date' => now(), 'slot' => ['08:00', '10:00'][$index], 'delay' => $index === 0 ? 5 : 0];
        }
        foreach (range(0, 4) as $index) {
            $rows[] = ['state' => 'scheduled', 'date' => now()->addDays($index === 0 ? 0 : $index), 'slot' => ['14:00', '15:00', '08:00', '10:00', '16:00'][$index]];
        }

        return collect($rows)->map(function (array $row, int $index) use ($types, $rooms, $veterinarians, $pets) {
            $start = Carbon::createFromFormat('H:i', $row['slot']);
            $duration = (int) ($types[$index % $types->count()]->estimated_duration ?: 60);
            $actualStart = in_array($row['state'], ['completed', 'in_progress'], true) ? $start->copy()->addMinutes($row['delay'] ?? 0) : null;
            $actualEnd = $actualStart?->copy()->addMinutes($duration);
            $date = $row['date'];
            if ($row['state'] === 'no_show') $date = now()->subDays(1);

            $surgery = Surgery::create([
                'pet_id' => $pets[$index % $pets->count()]->id,
                'veterinarian_id' => $veterinarians[$index % $veterinarians->count()]->id,
                'operating_room_id' => $rooms[$index % $rooms->count()]->id,
                'surgery_type_id' => $types[$index % $types->count()]->id,
                'scheduled_date' => $date->toDateString(),
                'start_time' => $start->format('H:i:s'),
                'end_time' => $start->copy()->addMinutes($duration)->format('H:i:s'),
                'actual_start_time' => $actualStart?->format('H:i:s'),
                'actual_end_time' => $actualEnd?->format('H:i:s'),
                'state' => $row['state'],
                'notes' => $row['notes'] ?? ($row['state'] === 'completed' ? 'Procedimiento finalizado con seguimiento registrado.' : null),
            ]);
            $surgery->forceFill(['created_at' => $date->copy()->subDays(2), 'updated_at' => now()])->saveQuietly();
            return $surgery;
        });
    }

    private function conflicts($surgeries, $rooms, $veterinarians): void
    {
        $active = $surgeries->where('state', 'scheduled')->values();
        foreach ([
            ['veterinarian', 0, 0, '14:15', '15:15'],
            ['operating_room', 0, 0, '14:30', '15:30'],
            ['veterinarian', 1, 1, '15:15', '16:00'],
            ['operating_room', 2, 2, '08:15', '09:15'],
        ] as [$type, $vetIndex, $roomIndex, $start, $end]) {
            $blocked = $active->first(fn (Surgery $surgery) => $surgery->veterinarian_id === $veterinarians[$vetIndex % $veterinarians->count()]->id || $surgery->operating_room_id === $rooms[$roomIndex % $rooms->count()]->id);
            SchedulingConflict::create([
                'scheduled_date' => $blocked?->scheduled_date ?? now()->addDay()->toDateString(),
                'start_time' => $start,
                'end_time' => $end,
                'conflict_type' => $type,
                'source' => 'example',
                'veterinarian_id' => $veterinarians[$vetIndex % $veterinarians->count()]->id,
                'operating_room_id' => $rooms[$roomIndex % $rooms->count()]->id,
                'details' => ['message' => 'Intento rechazado por cruce con cirugía programada.', 'blocked_by_surgery_id' => $blocked?->id],
            ]);
        }
    }

    private function mailEvidence($surgeries): void
    {
        $service = app(SurgeryEmailService::class);
        foreach ($surgeries->where('state', 'scheduled')->take(2) as $surgery) {
            $emails = collect([$surgery->pet?->owner?->email, $surgery->veterinarian?->email])->filter()->unique();
            foreach ($emails as $email) {
                if ($service->notifyParticipant($surgery, 'scheduled', $email)) {
                    DB::table('surgery_email_logs')->updateOrInsert(
                        ['surgery_id' => $surgery->id, 'recipient_email' => $email, 'notification_type' => 'scheduled'],
                        ['sent_at' => now(), 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
