<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Models\Specialty;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specialties = Specialty::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('state'), fn ($query, $state) => $query->where('state', $state))
            ->withCount('veterinarians')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stateOptions = ['active' => 'Activa', 'inactive' => 'Inactiva'];

        return view('specialties.index', compact('specialties', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialty = new Specialty();

        $stateOptions = [
            'active' => 'Activa',
            'inactive' => 'Inactiva',
        ];

        return view(
            'specialties.create',
            compact('specialty', 'stateOptions')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecialtyRequest $request)
    {
        $specialty = Specialty::create($request->validated());

        return redirect()
            ->route('specialties.show', $specialty)
            ->with('success', 'Especialidad creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialty $specialty)
    {
        $specialty->load('veterinarians');
        $specialty->loadCount('veterinarians');

        return view(
            'specialties.show',
            compact('specialty')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialty $specialty)
    {
        $stateOptions = [
            'active' => 'Activa',
            'inactive' => 'Inactiva',
        ];

        $specialty->loadCount('veterinarians');

        return view(
            'specialties.edit',
            compact('specialty', 'stateOptions')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialtyRequest $request, Specialty $specialty)
    {
        $specialty->update($request->validated());

        return redirect()
            ->route('specialties.show', $specialty)
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialty $specialty)
    {
        if ($specialty->veterinarians()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar una especialidad que tiene veterinarios asociados.'
            );
        }else {
            $specialty->delete();

            return redirect()
                ->route('specialties.index')
                ->with('success', 'Especialidad eliminada correctamente.');
        }
    }

    public function deactivate(Specialty $specialty)
    {
        if ($specialty->state === 'inactive') {
            return redirect()->route('specialties.index')->with('info', 'La especialidad ya se encuentra deshabilitada.');
        }

        $specialty->update(['state' => 'inactive']);

        return redirect()->route('specialties.index')->with('success', 'La especialidad fue deshabilitada correctamente.');
    }

    public function activate(Specialty $specialty)
    {
        if ($specialty->state === 'active') {
            return redirect()->route('specialties.index')->with('info', 'La especialidad ya se encuentra habilitada.');
        }

        $specialty->update(['state' => 'active']);

        return redirect()->route('specialties.index')->with('success', 'La especialidad fue habilitada correctamente.');
    }
}
