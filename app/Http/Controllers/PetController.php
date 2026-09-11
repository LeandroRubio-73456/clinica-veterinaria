<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Species;

class PetController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::query()
            ->with(['owner', 'species'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('breed', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('species', function ($speciesQuery) use ($search) {
                            $speciesQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(request('species_id'), fn($query, $speciesId) => $query->where('species_id', $speciesId))
            ->when(request('gender'), fn($query, $gender) => $query->where('gender', $gender))
            ->when(request('state'), fn($query, $state) => $query->where('state', $state))
            ->withCount('surgeries')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $speciesOptions = Species::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $genderOptions = ['male' => 'Macho', 'female' => 'Hembra'];
        $stateOptions = ['active' => 'Activo', 'inactive' => 'Inactivo'];

        return view('pets.index', compact('pets', 'speciesOptions', 'genderOptions', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owner = request()->filled('owner_id')
            ? Owner::query()->find(request()->integer('owner_id'))
            : null;

        $pet = new Pet([
            'owner_id' => $owner?->id,
        ]);

        $owners = Owner::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn($owner) => [
                $owner->id => "{$owner->first_name} {$owner->last_name}",
            ])
            ->all();

        $speciesOptions = Species::query()
            ->where('state', 'active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($species) => [
                $species->id => "{$species->name}",
            ])
            ->all();

        $genderOptions = [
            'male' => 'Macho',
            'female' => 'Hembra',
        ];

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('pets.create', compact(
            'pet',
            'owners',
            'speciesOptions',
            'genderOptions',
            'stateOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePetRequest $request)
    {
        $pet = Pet::create($request->validated());

        return redirect()
            ->route('pets.show', $pet)
            ->with('success', 'Mascota creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        $pet->load([
            'owner',
            'surgeries.surgeryType',
            'surgeries.veterinarian',
            'surgeries.operatingRoom',
        ]);

        $pet->loadCount('surgeries');

        return view('pets.show', compact('pet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pet $pet)
    {
        $owners = Owner::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn($owner) => [
                $owner->id => "{$owner->first_name} {$owner->last_name}",
            ])
            ->all();

        $speciesOptions = Species::query()
            ->where('state', 'active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($species) => [
                $species->id => "{$species->name}",
            ])
            ->all();

        $genderOptions = [
            'male' => 'Macho',
            'female' => 'Hembra',
        ];

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('pets.edit', compact(
            'pet',
            'owners',
            'speciesOptions',
            'genderOptions',
            'stateOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePetRequest $request, Pet $pet)
    {
        $pet->update($request->validated());

        return redirect()
            ->route('pets.show', $pet)
            ->with('success', 'Mascota actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        if ($pet->surgeries()->exists()) {
            return redirect()
                ->route('pets.index')
                ->with(
                    'error',
                    'No se puede eliminar la mascota porque tiene cirugías asociadas. Debe deshabilitarse para conservar su historial.'
                );
        }

        $pet->delete();

        return redirect()
            ->route('pets.index')
            ->with(
                'success',
                'Mascota eliminada correctamente.'
            );
    }

    public function deactivate(Pet $pet)
    {
        if ($pet->state === 'inactive') {
            return redirect()
                ->route('pets.index')
                ->with(
                    'info',
                    'La mascota ya se encuentra deshabilitada.'
                );
        }

        $pet->update([
            'state' => 'inactive',
        ]);

        return redirect()
            ->route('pets.index')
            ->with(
                'success',
                'La mascota fue deshabilitada correctamente.'
            );
    }

    public function activate(Pet $pet)
    {
        if ($pet->state === 'active') {
            return redirect()
                ->route('pets.index')
                ->with('info', 'La mascota ya se encuentra habilitada.');
        }

        $pet->update(['state' => 'active']);

        return redirect()
            ->route('pets.index')
            ->with('success', 'La mascota fue habilitada correctamente.');
    }
}
