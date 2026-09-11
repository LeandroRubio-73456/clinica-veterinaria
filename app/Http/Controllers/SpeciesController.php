<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpeciesRequest;
use App\Http\Requests\UpdateSpeciesRequest;
use App\Models\Species;

class SpeciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $species = Species::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('state'), fn ($query, $state) => $query->where('state', $state))
            ->withCount('pets')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stateOptions = ['active' => 'Activa', 'inactive' => 'Inactiva'];

        return view('species.index', compact('species', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $species = new Species();

        $stateOptions = [
            'active' => 'Activa',
            'inactive' => 'Inactiva',
        ];

        return view(
            'species.create',
            compact('species', 'stateOptions')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpeciesRequest $request)
    {
        $species = Species::create($request->validated());

        return redirect()
            ->route('species.show', $species)
            ->with('success', 'Especie creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Species $species)
    {
        $species->load('pets');
        $species->loadCount('pets');

        return view(
            'species.show',
            compact('species')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Species $species)
    {
        $stateOptions = [
            'active' => 'Activa',
            'inactive' => 'Inactiva',
        ];

        $species->loadCount('pets');

        return view(
            'species.edit',
            compact('species', 'stateOptions')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpeciesRequest $request, Species $species)
    {
        $species->update($request->validated());

        return redirect()
            ->route('species.show', $species)
            ->with('success', 'Especie actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Species $species)
    {
        if ($species->pets()->exists()) {
            return redirect()
                ->route('species.index')
                ->with('error', 'No se puede eliminar la especie porque tiene mascotas asociadas.');
        }
        $species->delete();

        return redirect()
            ->route('species.index')
            ->with('success', 'Especie eliminada correctamente.');
    }

    public function deactivate(Species $species)
    {
        if ($species->state === 'inactive') {
            return redirect()->route('species.index')->with('info', 'La especie ya se encuentra deshabilitada.');
        }

        $species->update(['state' => 'inactive']);

        return redirect()->route('species.index')->with('success', 'La especie fue deshabilitada correctamente.');
    }

    public function activate(Species $species)
    {
        if ($species->state === 'active') {
            return redirect()->route('species.index')->with('info', 'La especie ya se encuentra habilitada.');
        }

        $species->update(['state' => 'active']);

        return redirect()->route('species.index')->with('success', 'La especie fue habilitada correctamente.');
    }
}
