<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('roles.index', ['roles' => Role::withCount('users')->with('permissions')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('roles.form', ['role' => new Role(), 'permissions' => Permission::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $role = Role::create([...$data, 'slug' => Str::slug($data['name'])]);
        $role->permissions()->sync($request->input('permissions', []));
        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        return view('roles.form', ['role' => $role->load('permissions'), 'permissions' => Permission::orderBy('name')->get()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->is_system, 403, 'Los roles base no se pueden modificar.');
        $data = $this->validated($request);
        $role->update([...$data, 'slug' => Str::slug($data['name'])]);
        $role->permissions()->sync($request->input('permissions', []));
        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->is_system || $role->users()->exists(), 403, 'Este rol está protegido o tiene usuarios asignados.');
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:80'], 'description' => ['nullable', 'string', 'max:500']]);
    }
}
