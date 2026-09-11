<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperatingRoomRequest;
use App\Http\Requests\UpdateOperatingRoomRequest;
use App\Models\OperatingRoom;

class OperatingRoomController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operatingRooms = OperatingRoom::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->when(request('type'), fn ($query, $type) => $query->where('type', $type))
            ->when(request('state'), fn ($query, $state) => $query->where('state', $state))
            ->withCount('surgeries')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $typeOptions = OperatingRoom::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type', 'type')->toArray();
        $stateOptions = [
            'available' => 'Disponible',
            'occupied' => 'Ocupado',
            'maintenance' => 'Mantenimiento',
            'inactive' => 'Desactivado',
        ];

        return view('operating-rooms.index', compact('operatingRooms', 'typeOptions', 'stateOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $operatingRoom = new OperatingRoom();

        $stateOptions = [
            'available' => 'Disponible',
            'occupied' => 'Ocupado',
            'maintenance' => 'Mantenimiento',
            'inactive' => 'Desactivado',
        ];

        return view(
            'operating-rooms.create',
            compact('operatingRoom', 'stateOptions')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperatingRoomRequest $request)
    {
        $operatingRoom = OperatingRoom::create($request->validated());

        return redirect()
            ->route('operating-rooms.show', $operatingRoom)
            ->with('success', 'Quirofano creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OperatingRoom $operatingRoom)
    {
        $operatingRoom->load([
            'surgeries.pet',
            'surgeries.veterinarian',
            'surgeries.surgeryType',
        ]);

        $operatingRoom->loadCount('surgeries');

        return view(
            'operating-rooms.show',
            compact('operatingRoom')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OperatingRoom $operatingRoom)
    {
        $stateOptions = [
            'available' => 'Disponible',
            'occupied' => 'Ocupado',
            'maintenance' => 'Mantenimiento',
            'inactive' => 'Desactivado',
        ];

        return view(
            'operating-rooms.edit',
            compact('operatingRoom', 'stateOptions')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperatingRoomRequest $request, OperatingRoom $operatingRoom)
    {
        $operatingRoom->update($request->validated());

        return redirect()
            ->route('operating-rooms.show', $operatingRoom)
            ->with('success', 'Quirofano actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OperatingRoom $operatingRoom)
    {
        if ($operatingRoom->surgeries()->exists()) {
            return redirect()
                ->route('operating-rooms.index')
                ->with(
                    'error',
                    'No se puede eliminar el quirófano porque tiene cirugías asociadas. Debe deshabilitarse para conservar el historial.'
                );
        }

        $operatingRoom->delete();

        return redirect()
            ->route('operating-rooms.index')
            ->with(
                'success',
                'Quirófano eliminado correctamente.'
            );
    }

    public function deactivate(OperatingRoom $operatingRoom)
    {
        if ($operatingRoom->state === 'inactive') {
            return redirect()
                ->route('operating-rooms.index')
                ->with(
                    'info',
                    'El quirófano ya se encuentra deshabilitado.'
                );
        }

        $operatingRoom->update([
            'state' => 'inactive',
        ]);

        return redirect()
            ->route('operating-rooms.index')
            ->with(
                'success',
                'El quirófano fue deshabilitado correctamente.'
            );
    }

    public function activate(OperatingRoom $operatingRoom)
    {
        if ($operatingRoom->state !== 'inactive') {
            return redirect()
                ->route('operating-rooms.index')
                ->with('info', 'El quirófano ya está habilitado para la operación.');
        }

        $operatingRoom->update(['state' => 'available']);

        return redirect()
            ->route('operating-rooms.index')
            ->with('success', 'El quirófano fue habilitado y quedó disponible.');
    }
}
