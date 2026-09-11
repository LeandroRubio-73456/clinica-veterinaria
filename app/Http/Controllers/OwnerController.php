<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOwnerRequest;
use App\Http\Requests\UpdateOwnerRequest;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = Owner::query()
            ->withCount('pets')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cedula', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owner = new Owner();

        return view('owners.create', compact('owner'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOwnerRequest $request)
    {
        $validated = $request->validated();

        $owner = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'username' => $validated['cedula'],
                'email' => $validated['email'],
                // El enum legado `role` no contiene 'propietario'; el rol real
                // se resuelve mediante `role_id` mientras se mantiene compatibilidad.
                'role' => 'administrativo',
                'role_id' => Role::where('slug', 'propietario')->value('id'),
                'password' => Hash::make($validated['cedula']),
            ]);

            return Owner::create([...$validated, 'user_id' => $user->id]);
        });

        return redirect()
            ->route('owners.show', $owner)
            ->with('success', 'Dueño creado correctamente. Usuario inicial: su cédula.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Owner $owner)
    {
        $owner->load('pets');

        return view('owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOwnerRequest $request, Owner $owner)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $owner) {
            $owner->update($validated);

            if ($user = $owner->user) {
                $user->update([
                    'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                    'username' => $validated['cedula'],
                    'email' => $validated['email'],
                ]);
            }
        });

        return redirect()
            ->route('owners.show', $owner)
            ->with('success', 'Dueño actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Owner $owner)
    {
        $owner->delete();

        return redirect()
            ->route('owners.index')
            ->with('success', 'Dueño eliminado correctamente.');
    }
}
