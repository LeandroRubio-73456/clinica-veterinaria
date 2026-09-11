<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVeterinarianRequest;
use App\Http\Requests\UpdateVeterinarianRequest;
use App\Models\Specialty;
use App\Models\Veterinarian;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VeterinarianController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $veterinarians = Veterinarian::query()
            ->with('specialty')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cedula', 'like', "%{$search}%")
                        ->orWhereHas('specialty', fn($specialty) => $specialty->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(request('specialty_id'), fn($query, $specialtyId) => $query->where('specialty_id', $specialtyId))
            ->when(request('state'), fn($query, $state) => $query->where('state', $state))
            ->withCount('surgeries')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $specialtyOptions = Specialty::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $stateOptions = ['active' => 'Activo', 'inactive' => 'Inactivo'];

        return view('veterinarians.index', compact('veterinarians', 'specialtyOptions', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $veterinarian = new Veterinarian();

        $specialtyOptions = Specialty::query()
            ->where('state', 'active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($specialty) => [
                $specialty->id => "{$specialty->name}",
            ])
            ->all();

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('veterinarians.create', compact(
            'veterinarian',
            'specialtyOptions',
            'stateOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVeterinarianRequest $request)
    {
        $validated = $request->validated();

        $veterinarian = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'username' => $validated['cedula'],
                'email' => $validated['email'],
                'role' => 'veterinario',
                'role_id' => Role::where('slug', 'veterinario')->value('id'),
                'password' => Hash::make($validated['cedula']),
            ]);

            return Veterinarian::create([...$validated, 'user_id' => $user->id]);
        });

        return redirect()
            ->route('veterinarians.show', $veterinarian)
            ->with('success', 'Veterinario creado correctamente. Usuario inicial: su cédula.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Veterinarian $veterinarian)
    {
        $veterinarian->load([
            'surgeries.pet',
            'surgeries.surgeryType',
            'surgeries.operatingRoom',
        ]);

        $veterinarian->loadCount('surgeries');

        return view(
            'veterinarians.show',
            compact('veterinarian')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Veterinarian $veterinarian)
    {

        $specialtyOptions = Specialty::query()
            ->where('state', 'active')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($specialty) => [
                $specialty->id => "{$specialty->name}",
            ])
            ->all();

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('veterinarians.edit', compact(
            'veterinarian',
            'specialtyOptions',
            'stateOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVeterinarianRequest $request, Veterinarian $veterinarian)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $veterinarian) {
            $veterinarian->update($validated);

            if ($user = $veterinarian->user) {
                $user->update([
                    'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                    'username' => $validated['cedula'],
                    'email' => $validated['email'],
                ]);
            }
        });

        return redirect()
            ->route('veterinarians.show', $veterinarian)
            ->with('success', 'Veterinario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veterinarian $veterinarian)
    {
        if ($veterinarian->surgeries()->exists()) {
            return redirect()
                ->route('veterinarians.index')
                ->with(
                    'error',
                    'No se puede eliminar el veterinario porque tiene cirugías asociadas. Debe ser desactivado.'
                );
        }

        $veterinarian->delete();

        return redirect()
            ->route('veterinarians.index')
            ->with(
                'success',
                'Veterinario eliminado correctamente.'
            );
    }

    public function deactivate(Veterinarian $veterinarian)
    {
        if ($veterinarian->state === 'inactive') {
            return redirect()
                ->route('veterinarians.index')
                ->with(
                    'info',
                    'El veterinario ya se encuentra desactivado.'
                );
        }

        $veterinarian->update([
            'state' => 'inactive',
        ]);

        return redirect()
            ->route('veterinarians.index')
            ->with(
                'success',
                'El veterinario fue desactivado correctamente.'
            );
    }

    public function activate(Veterinarian $veterinarian)
    {
        if ($veterinarian->state === 'active') {
            return redirect()
                ->route('veterinarians.index')
                ->with('info', 'El veterinario ya se encuentra habilitado.');
        }

        $veterinarian->update(['state' => 'active']);

        return redirect()
            ->route('veterinarians.index')
            ->with('success', 'El veterinario fue habilitado correctamente.');
    }
}
