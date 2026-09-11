<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurgeryTypeRequest;
use App\Http\Requests\UpdateSurgeryTypeRequest;
use App\Models\SurgeryType;
use Illuminate\Database\QueryException;

class SurgeryTypeController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surgeryTypes = SurgeryType::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('state'), fn ($query, $state) => $query->where('state', $state))
            ->withCount('surgeries')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stateOptions = ['active' => 'Activo', 'inactive' => 'Inactivo'];

        return view('surgery-types.index', compact('surgeryTypes', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $surgeryType = new SurgeryType();

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('surgery-types.create', compact('surgeryType', 'stateOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurgeryTypeRequest $request)
    {
        $surgeryType = SurgeryType::create($request->validated());

        return redirect()
            ->route('surgery-types.show', $surgeryType)
            ->with('success', 'Tipo de cirugía creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SurgeryType $surgeryType)
    {
        $surgeryType->load([
            'surgeries.pet',
            'surgeries.veterinarian',
        ]);

        $surgeryType->loadCount('surgeries');

        return view('surgery-types.show', compact('surgeryType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurgeryType $surgeryType)
    {
        $surgeryType->loadCount('surgeries');

        $stateOptions = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ];

        return view('surgery-types.edit', compact('surgeryType', 'stateOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurgeryTypeRequest $request, SurgeryType $surgeryType)
    {
        $surgeryType->update($request->validated());

        return redirect()
            ->route('surgery-types.show', $surgeryType)
            ->with('success', 'Tipo de cirugía actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurgeryType $surgeryType)
    {
        if ($surgeryType->surgeries()->exists()) {
            return redirect()
                ->route('surgery-types.index')
                ->with(
                    'error',
                    'No se puede eliminar el tipo de cirugía porque tiene cirugías asociadas. Debe deshabilitarse para conservar el historial.'
                );
        }

        $surgeryType->delete();

        return redirect()
            ->route('surgery-types.index')
            ->with(
                'success',
                'Tipo de cirugía eliminado correctamente.'
            );
    }

    public function deactivate(SurgeryType $surgeryType)
    {
        if ($surgeryType->state === 'inactive') {
            return redirect()
                ->route('surgery-types.index')
                ->with('info', 'El tipo de cirugía ya se encuentra deshabilitado.');
        }

        $surgeryType->update([
            'state' => 'inactive',
        ]);

        return redirect()
            ->route('surgery-types.index')
            ->with(
                'success',
                'El tipo de cirugía fue deshabilitado correctamente.'
            );
    }

    public function activate(SurgeryType $surgeryType)
    {
        if ($surgeryType->state === 'active') {
            return redirect()->route('surgery-types.index')->with('info', 'El tipo de cirugía ya se encuentra habilitado.');
        }

        $surgeryType->update(['state' => 'active']);

        return redirect()->route('surgery-types.index')->with('success', 'El tipo de cirugía fue habilitado correctamente.');
    }
}
