<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Veterinarian;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['veterinarian', 'roleProfile'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(request('role'), fn ($query, $role) => $query->whereHas('roleProfile', fn ($q) => $q->where('slug', $role))->orWhere('role', $role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roleOptions = Role::query()->orderBy('name')->pluck('name', 'slug')->toArray();

        return view('users.index', compact('users', 'roleOptions'));
    }

    public function create(User $user): View
    {
        $veterinarianOptions = $this->veterinarianOptions($user);

        $roleOptions = Role::query()->where('slug', '!=', 'propietario')->orderBy('name')->pluck('name', 'slug')->toArray();
        return view('users.create', compact('user', 'veterinarianOptions', 'roleOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => [
                'required',
                Rule::exists('roles', 'slug')->where(fn ($query) => $query->where('slug', '!=', 'propietario')),
            ],
            'veterinarian_id' => [
                'nullable',
                'required_if:role,veterinario',
                'prohibited_unless:role,veterinario',
                Rule::exists('veterinarians', 'id')->where(fn ($query) => $query
                    ->where('state', 'active')
                    ->whereNull('user_id')),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => in_array($validated['role'], ['admin', 'administrativo', 'veterinario'], true) ? $validated['role'] : 'administrativo',
                'role_id' => Role::where('slug', $validated['role'])->value('id'),
                'password' => Hash::make($validated['password']),
            ]);

            if ($validated['role'] === 'veterinario') {
                Veterinarian::query()
                    ->whereKey($validated['veterinarian_id'])
                    ->whereNull('user_id')
                    ->update(['user_id' => $user->id]);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('veterinarian');
        $veterinarianOptions = $this->veterinarianOptions($user);

        $roleOptions = Role::query()->where('slug', '!=', 'propietario')->orderBy('name')->pluck('name', 'slug')->toArray();
        return view('users.edit', compact('user', 'veterinarianOptions', 'roleOptions'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::exists('roles', 'slug')->where(fn ($query) => $query->where('slug', '!=', 'propietario')),
            ],
            'veterinarian_id' => [
                'nullable',
                'required_if:role,veterinario',
                'prohibited_unless:role,veterinario',
                Rule::exists('veterinarians', 'id')->where(function ($query) use ($user) {
                    $query
                        ->where('state', 'active')
                        ->where(function ($query) use ($user) {
                            $query->whereNull('user_id')
                                ->orWhere('user_id', $user->id);
                        });
                }),
            ],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => in_array($validated['role'], ['admin', 'administrativo', 'veterinario'], true) ? $validated['role'] : 'administrativo',
                'role_id' => Role::where('slug', $validated['role'])->value('id'),
            ]);

            if (!empty($validated['password'])) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            $user->veterinarian()->update(['user_id' => null]);

            if ($validated['role'] === 'veterinario') {
                Veterinarian::query()
                    ->whereKey($validated['veterinarian_id'])
                    ->where(function ($query) use ($user) {
                        $query->whereNull('user_id')
                            ->orWhere('user_id', $user->id);
                    })
                    ->update(['user_id' => $user->id]);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->guard()->user())) {
            return back()->with(
                'error',
                'No puedes eliminar tu propio usuario.'
            );
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    private function veterinarianOptions(User $user): array
    {
        return Veterinarian::query()
            ->where('state', 'active')
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->when($user->exists, fn ($query) => $query->orWhere('user_id', $user->id));
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn (Veterinarian $veterinarian) => [
                $veterinarian->id => "{$veterinarian->first_name} {$veterinarian->last_name}",
            ])
            ->toArray();
    }
}
