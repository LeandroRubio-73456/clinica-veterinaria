<?php

use App\Models\OperatingRoom;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\Species;
use App\Models\Surgery;
use App\Models\SurgeryType;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

function userWithRole(string $role): User
{
    $user = User::factory()->create([
        // La columna legado `users.role` no contiene propietario; ese rol
        // se resuelve mediante la relación dinámica `role_id`.
        'role' => $role === 'propietario' ? 'administrativo' : $role,
        'role_id' => Role::query()->where('slug', $role)->value('id'),
    ]);

    return $user;
}

test('el propietario solo consulta sus propias cirugías en el portal', function () {
    $ownerUser = userWithRole('propietario');
    $owner = Owner::factory()->create(['user_id' => $ownerUser->id]);
    $otherOwner = Owner::factory()->create();
    $species = Species::factory()->create();
    $type = SurgeryType::factory()->create();
    $room = OperatingRoom::factory()->create();
    $veterinarian = Veterinarian::factory()->create();
    $ownPet = Pet::factory()->create(['owner_id' => $owner->id, 'species_id' => $species->id]);
    $otherPet = Pet::factory()->create(['owner_id' => $otherOwner->id, 'species_id' => $species->id]);
    Surgery::factory()->create(['pet_id' => $ownPet->id, 'surgery_type_id' => $type->id, 'operating_room_id' => $room->id, 'veterinarian_id' => $veterinarian->id, 'scheduled_date' => now()->addDay()->toDateString(), 'state' => 'scheduled']);
    Surgery::factory()->create(['pet_id' => $otherPet->id, 'surgery_type_id' => $type->id, 'operating_room_id' => $room->id, 'veterinarian_id' => $veterinarian->id, 'scheduled_date' => now()->addDay()->toDateString(), 'state' => 'scheduled']);

    $response = $this->actingAs($ownerUser)->get('/mi-portal');

    $response->assertOk()->assertSee($ownPet->name)->assertDontSee($otherPet->name);
});

test('un veterinario no puede consultar reportes administrativos', function () {
    $response = $this->actingAs(userWithRole('veterinario'))->get('/reports');

    $response->assertForbidden();
});

test('un usuario administrativo puede consultar reportes', function () {
    $response = $this->actingAs(userWithRole('administrativo'))->get('/reports?period=day');

    $response->assertOk()->assertSee('Indicadores operativos');
});

test('un conflicto de agenda se registra cuando se intenta reutilizar un recurso ocupado', function () {
    Notification::fake();
    $admin = userWithRole('admin');
    $owner = Owner::factory()->create();
    $species = Species::factory()->create();
    $type = SurgeryType::factory()->create();
    $room = OperatingRoom::factory()->create(['state' => 'available']);
    $veterinarian = Veterinarian::factory()->create(['state' => 'active']);
    $firstPet = Pet::factory()->create(['owner_id' => $owner->id, 'species_id' => $species->id]);
    $secondPet = Pet::factory()->create(['owner_id' => $owner->id, 'species_id' => $species->id]);
    $date = now()->addDay()->toDateString();
    Surgery::factory()->create([
        'pet_id' => $firstPet->id,
        'surgery_type_id' => $type->id,
        'operating_room_id' => $room->id,
        'veterinarian_id' => $veterinarian->id,
        'scheduled_date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'state' => 'scheduled',
    ]);

    $response = $this->actingAs($admin)->post('/surgeries', [
        'pet_id' => $secondPet->id,
        'surgery_type_id' => $type->id,
        'operating_room_id' => $room->id,
        'veterinarian_id' => $veterinarian->id,
        'scheduled_date' => $date,
        'start_time' => '09:30',
        'end_time' => '10:30',
    ]);

    $response->assertSessionHasErrors();
    expect(DB::table('scheduling_conflicts')->count())->toBe(1);
});

test('el scheduler marca como no presentada una cirugía fuera de tolerancia', function () {
    Notification::fake();
    $surgery = Surgery::factory()->create([
        'scheduled_date' => now()->toDateString(),
        'start_time' => now()->subMinutes(30)->format('H:i:s'),
        'end_time' => now()->subMinutes(10)->format('H:i:s'),
        'state' => 'scheduled',
    ]);

    Artisan::call('surgeries:mark-no-show');

    expect($surgery->fresh()->state)->toBe('no_show');
});
