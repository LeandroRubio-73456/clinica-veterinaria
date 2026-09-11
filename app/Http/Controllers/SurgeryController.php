<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreSurgeryRequest;
use App\Http\Requests\UpdateSurgeryRequest;
use App\Models\OperatingRoom;
use App\Models\Owner;
use App\Models\Surgery;
use App\Models\SurgeryType;
use App\Models\SchedulingConflict;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\SurgeryEmailService;

/**
 * Gestiona la agenda, el ciclo de vida y las validaciones de las cirugías.
 *
 * La programación se realiza dentro de transacciones para evitar que dos
 * solicitudes concurrentes reserven el mismo veterinario o quirófano.
 */
class SurgeryController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authenticatedUser = auth()->user();
        $assignedVeterinarianId = $authenticatedUser->isVeterinarian()
            ? ($authenticatedUser->veterinarian?->state === 'active'
                ? $authenticatedUser->veterinarian->id
                : null)
            : null;

        request()->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'veterinarian_id' => ['nullable', 'exists:veterinarians,id'],
            'operating_room_id' => ['nullable', 'exists:operating_rooms,id'],
            'state' => ['nullable', 'in:scheduled,in_progress,completed,cancelled,no_show'],
        ]);

        $surgeries = Surgery::query()
            ->with([
                'pet.owner',
                'veterinarian',
                'operatingRoom',
                'surgeryType',
            ])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('pet', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })->orWhereHas('surgeryType', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when(request('from'), fn ($query, $from) => $query->whereDate('scheduled_date', '>=', $from))
            ->when(request('to'), fn ($query, $to) => $query->whereDate('scheduled_date', '<=', $to))
            ->when(request('veterinarian_id'), fn ($query, $id) => $query->where('veterinarian_id', $id))
            ->when(request('operating_room_id'), fn ($query, $id) => $query->where('operating_room_id', $id))
            ->when(request('state'), fn ($query, $state) => $query->where('state', $state))
            ->when($authenticatedUser->isVeterinarian(), fn ($query) => $query->where('veterinarian_id', $assignedVeterinarianId ?? 0))
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $veterinarianOptions = Veterinarian::query()
            ->when($authenticatedUser->isVeterinarian(), fn ($query) => $query->whereKey($assignedVeterinarianId ?? 0))
            ->orderBy('first_name')->orderBy('last_name')
            ->get()->mapWithKeys(fn ($vet) => [$vet->id => "{$vet->first_name} {$vet->last_name}"])->toArray();
        $operatingRoomOptions = OperatingRoom::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $stateOptions = [
            'scheduled' => 'Programada',
            'in_progress' => 'En curso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No presentada',
        ];

        return view('surgeries.index', compact('surgeries', 'veterinarianOptions', 'operatingRoomOptions', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $surgery = new Surgery([
            'pet_id' => request()->filled('pet_id')
                ? request()->integer('pet_id')
                : null,
        ]);

        [$ownerOptions, $petsByOwner] = $this->ownerPetSchedulingOptions();

        $veterinarianOptions = Veterinarian::query()
            ->where('state', 'active')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn($vet) => [$vet->id => "{$vet->first_name} {$vet->last_name}"])
            ->toArray();

        $operatingRoomOptions = OperatingRoom::query()
            ->whereNotIn('state', ['maintenance', 'inactive'])
            ->orderByRaw("CASE WHEN state = 'available' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($room) => [
                $room->id => "{$room->name} ({$this->roomStateLabel($room->state)})",
            ])
            ->toArray();

        $surgeryTypeOptions = SurgeryType::query()
            ->where('state', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $surgeryTypeDurations = SurgeryType::query()
            ->pluck('estimated_duration', 'id')
            ->map(fn ($duration) => (int) ($duration ?: 60))
            ->toArray();

        return view('surgeries.create', compact(
            'surgery',
            'ownerOptions',
            'petsByOwner',
            'veterinarianOptions',
            'operatingRoomOptions',
            'surgeryTypeOptions',
            'surgeryTypeDurations',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurgeryRequest $request)
    {
        $validated = $request->validated();

        try {
            $surgery = DB::transaction(function () use ($validated) {
                $this->lockSchedulingResources($validated);
                $this->validateAvailability($validated);

                return Surgery::create([
                    ...$validated,
                    'state' => 'scheduled',
                ]);
            });
        } catch (ValidationException $exception) {
            $this->recordSchedulingConflict($validated, $exception, source: 'application');

            throw $exception;
        }

        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'scheduled');

        return redirect()
            ->route('surgeries.index')
            ->with('success', 'La cirugía fue programada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Surgery $surgery)
    {
        $surgery->load([
            'pet.owner',
            'veterinarian',
            'operatingRoom',
            'surgeryType',
        ]);

        return view('surgeries.show', compact('surgery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Surgery $surgery)
    {
        if ($surgery->state !== 'scheduled') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo las cirugías programadas pueden editarse.');
        }

        [$ownerOptions, $petsByOwner] = $this->ownerPetSchedulingOptions();

        $veterinarianOptions = Veterinarian::query()
            ->where('state', 'active')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn($vet) => [$vet->id => "{$vet->first_name} {$vet->last_name}"])
            ->toArray();

        $operatingRoomOptions = OperatingRoom::query()
            ->whereNotIn('state', ['maintenance', 'inactive'])
            ->orderByRaw("CASE WHEN state = 'available' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($room) => [
                $room->id => "{$room->name} ({$this->roomStateLabel($room->state)})",
            ])
            ->toArray();

        $surgeryTypeOptions = SurgeryType::query()
            ->where(function ($query) use ($surgery) {
                $query->where('state', 'active')
                    ->orWhere('id', $surgery->surgery_type_id);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $surgeryTypeDurations = SurgeryType::query()
            ->pluck('estimated_duration', 'id')
            ->map(fn ($duration) => (int) ($duration ?: 60))
            ->toArray();

        return view('surgeries.edit', compact(
            'surgery',
            'ownerOptions',
            'petsByOwner',
            'veterinarianOptions',
            'operatingRoomOptions',
            'surgeryTypeOptions',
            'surgeryTypeDurations',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurgeryRequest $request, Surgery $surgery)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $surgery) {
                $lockedSurgery = Surgery::query()
                    ->whereKey($surgery->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->lockSchedulingResources($validated);
                $this->validateAvailability($validated, $lockedSurgery);
                $lockedSurgery->update($validated);
            });
        } catch (ValidationException $exception) {
            $this->recordSchedulingConflict($validated, $exception, $surgery, 'update');

            throw $exception;
        }

        return redirect()
            ->route('surgeries.show', $surgery)
            ->with('success', 'La cirugía fue actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Surgery $surgery)
    {
        if ($surgery->state !== 'scheduled') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo las cirugías programadas pueden eliminarse.');
        }

        $surgery->delete();

        return redirect()
            ->route('surgeries.index')
            ->with('success', 'Cirugía eliminada correctamente.');
    }

    private function validateAvailability(
        array $data,
        ?Surgery $surgery = null
    ): void {
        $baseQuery = Surgery::query()
            ->whereDate('scheduled_date', $data['scheduled_date'])
            ->whereNotIn('state', ['cancelled', 'no_show'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($surgery, function ($query) use ($surgery) {
                $query->whereKeyNot($surgery->getKey());
            });

        $veterinarianConflict = (clone $baseQuery)
            ->where('veterinarian_id', $data['veterinarian_id'])
            ->lockForUpdate()
            ->first() !== null;

        $operatingRoomConflict = (clone $baseQuery)
            ->where('operating_room_id', $data['operating_room_id'])
            ->lockForUpdate()
            ->first() !== null;

        if ($veterinarianConflict) {
            throw ValidationException::withMessages([
                'veterinarian_id' =>
                'El veterinario seleccionado ya tiene una cirugía programada en este horario.',
            ]);
        }

        if ($operatingRoomConflict) {
            throw ValidationException::withMessages([
                'operating_room_id' =>
                'El quirófano seleccionado ya está ocupado en este horario.',
            ]);
        }
    }

    private function lockSchedulingResources(array $data): void
    {
        Veterinarian::query()
            ->whereKey($data['veterinarian_id'])
            ->lockForUpdate()
            ->firstOrFail();

        OperatingRoom::query()
            ->whereKey($data['operating_room_id'])
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function recordSchedulingConflict(
        array $data,
        ValidationException $exception,
        ?Surgery $surgery = null,
        string $source = 'application'
    ): void {
        $errors = $exception->errors();
        $conflictType = match (true) {
            isset($errors['veterinarian_id']) => 'veterinarian',
            isset($errors['operating_room_id']) => 'operating_room',
            default => null,
        };

        if ($conflictType === null) {
            return;
        }

        SchedulingConflict::create([
            'scheduled_date' => $data['scheduled_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'conflict_type' => $conflictType,
            'source' => $source,
            'veterinarian_id' => $data['veterinarian_id'] ?? null,
            'operating_room_id' => $data['operating_room_id'] ?? null,
            'surgery_id' => $surgery?->getKey(),
            'user_id' => auth()->id(),
            'details' => $errors,
        ]);
    }

    public function start(Surgery $surgery): RedirectResponse
    {
        if ($surgery->state !== 'scheduled') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo se puede iniciar una cirugía que esté programada.');
        }

        DB::transaction(function () use ($surgery) {
            $surgery->update([
                'state' => 'in_progress',
                'actual_start_time' => now()->format('H:i:s'),
            ]);

            $surgery->operatingRoom()->update([
                'state' => 'occupied',
            ]);
        });

        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'updated');

        return redirect()
            ->route('surgeries.show', $surgery)
            ->with('success', 'La cirugía ha sido iniciada correctamente.');
    }

    public function complete(Surgery $surgery): RedirectResponse
    {
        if ($surgery->state !== 'in_progress') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo se puede finalizar una cirugía que esté en curso.');
        }

        DB::transaction(function () use ($surgery) {
            $surgery->update([
                'state' => 'completed',
                'actual_end_time' => now()->format('H:i:s'),
            ]);

            $surgery->operatingRoom()->update([
                'state' => 'available',
            ]);
        });

        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'updated');

        return redirect()
            ->route('surgeries.show', $surgery)
            ->with('success', 'La cirugía ha sido finalizada correctamente.');
    }

    public function cancel(\Illuminate\Http\Request $request, Surgery $surgery): RedirectResponse
    {
        if ($surgery->state !== 'scheduled') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo se puede cancelar una cirugía que esté programada.');
        }

        $validated = $request->validate(['notes' => ['nullable', 'string', 'max:1000']]);
        DB::transaction(function () use ($surgery, $validated) {
            $surgery->update([
                'state' => 'cancelled',
                'notes' => $validated['notes'] ?? $surgery->notes ?? 'Motivo no especificado.',
            ]);

            $surgery->operatingRoom()->update([
                'state' => 'available',
            ]);
        });

        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'cancelled');

        return redirect()
            ->route('surgeries.show', $surgery)
            ->with('success', 'La cirugía ha sido cancelada.');
    }

    public function noShow(\Illuminate\Http\Request $request, Surgery $surgery): RedirectResponse
    {
        if ($surgery->state !== 'scheduled') {
            return redirect()
                ->route('surgeries.show', $surgery)
                ->with('error', 'Solo se puede marcar como no presentado una cirugía programada.');
        }

        $validated = $request->validate(['notes' => ['nullable', 'string', 'max:1000']]);
        DB::transaction(function () use ($surgery, $validated) {
            $surgery->update([
                'state' => 'no_show',
                'notes' => $validated['notes'] ?? $surgery->notes ?? 'Motivo no especificado.',
            ]);

            $surgery->operatingRoom()->update([
                'state' => 'available',
            ]);
        });

        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'no_show');

        return redirect()
            ->route('surgeries.show', $surgery)
            ->with('success', 'La cirugía ha sido marcada como no presentada.');
    }

    public function calendar()
    {
        return view('surgeries.calendar');
    }

    public function calendarEvents()
    {
        $authenticatedUser = auth()->user();
        $assignedVeterinarianId = $authenticatedUser->isVeterinarian()
            ? ($authenticatedUser->veterinarian?->state === 'active'
                ? $authenticatedUser->veterinarian->id
                : null)
            : null;

        $surgeries = Surgery::query()
            ->with([
                'pet.owner',
                'veterinarian',
                'operatingRoom',
                'surgeryType',
            ])
            ->whereNotNull('scheduled_date')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->when($authenticatedUser->isVeterinarian(), fn ($query) => $query->where('veterinarian_id', $assignedVeterinarianId ?? 0))
            ->get();

        $events = $surgeries->map(function (Surgery $surgery) {
            return [
                'id' => (string) $surgery->id,

                'title' => $surgery->pet->name
                    ?? 'Cirugía #' . $surgery->id,

                'start' => $surgery->scheduled_date
                    . 'T'
                    . $surgery->start_time,

                'end' => $surgery->scheduled_date
                    . 'T'
                    . $surgery->end_time,

                'url' => route('surgeries.show', $surgery),

                'extendedProps' => [
                    'state' => $surgery->state,
                    'veterinarian_id' => (string) $surgery->veterinarian_id,
                    'operating_room_id' => (string) $surgery->operating_room_id,
                    'surgery_type_id' => (string) $surgery->surgery_type_id,
                    'veterinarian' => $surgery->veterinarian->first_name . ' ' . $surgery->veterinarian->last_name ?? null,
                    'operating_room' => $surgery->operatingRoom->name ?? null,
                    'surgery_type' => $surgery->surgeryType->name ?? null,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Construye las opciones de dueños y el listado de mascotas agrupado por
     * dueño, usadas por el selector en cascada del formulario de cirugías.
     *
     * @return array{0: array<int, string>, 1: array<int, array<int, array{id: int, label: string}>>}
     */
    private function ownerPetSchedulingOptions(): array
    {
        $owners = Owner::query()
            ->whereHas('pets')
            ->with(['pets' => fn ($query) => $query->orderBy('name')])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $ownerOptions = $owners
            ->mapWithKeys(fn ($owner) => [
                $owner->id => trim(($owner->cedula ? "{$owner->cedula} — " : '') . "{$owner->first_name} {$owner->last_name}"),
            ])
            ->toArray();

        $petsByOwner = $owners
            ->mapWithKeys(fn ($owner) => [
                $owner->id => $owner->pets
                    ->map(fn ($pet) => [
                        'id' => $pet->id,
                        'label' => $pet->breed ? "{$pet->name} ({$pet->breed})" : $pet->name,
                    ])
                    ->values()
                    ->all(),
            ])
            ->toArray();

        return [$ownerOptions, $petsByOwner];
    }

    private function roomStateLabel(string $state): string
    {
        return [
            'available' => 'Disponible',
            'occupied' => 'Ocupado actualmente',
        ][$state] ?? ucfirst($state);
    }
}
