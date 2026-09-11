<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\NotificationService;
use App\Models\Surgery;

class OwnerPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $owner = $request->user()->owner;
        abort_unless($owner, 403, 'La cuenta no tiene un perfil de propietario asociado.');

        $surgeries = $owner->surgeries()
            ->with(['pet.species', 'surgeryType', 'veterinarian'])
            ->whereDate('surgeries.scheduled_date', '>=', now()->toDateString())
            ->whereNotIn('surgeries.state', ['cancelled', 'no_show'])
            ->orderBy('surgeries.scheduled_date')->orderBy('surgeries.start_time')
            ->get();

        $surgeryHistory = $owner->surgeries()
            ->with(['pet.species', 'surgeryType', 'veterinarian'])
            ->where(function ($query) {
                $query->whereDate('surgeries.scheduled_date', '<', now()->toDateString())
                    ->orWhereIn('surgeries.state', ['cancelled', 'no_show']);
            })
            ->orderByDesc('surgeries.scheduled_date')
            ->orderByDesc('surgeries.start_time')
            ->get();

        $pets = $owner->pets()
            ->with('species')
            ->orderBy('name')
            ->get();

        return view('portal.dashboard', [
            'owner' => $owner,
            'surgeries' => $surgeries,
            'surgeryHistory' => $surgeryHistory,
            'pets' => $pets,
            'notifications' => app(NotificationService::class)->for($request->user()),
        ]);
    }

    public function surgery(Request $request, Surgery $surgery): View
    {
        $owner = $request->user()->owner;
        abort_unless($owner, 403, 'La cuenta no tiene un perfil de propietario asociado.');

        $surgery->loadMissing(['pet.species', 'pet.owner', 'surgeryType', 'veterinarian', 'operatingRoom']);
        abort_unless($surgery->pet?->owner_id === $owner->id, 403);

        return view('portal.surgery', compact('surgery'));
    }

    public function profile(Request $request): View
    {
        return view('portal.profile', ['user' => $request->user(), 'owner' => $request->user()->owner]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $owner = $user->owner;
        abort_unless($owner, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id, 'unique:owners,email,' . $owner->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        $owner->update(collect($data)->only(['first_name', 'last_name', 'email', 'phone', 'address'])->all());

        return Redirect::route('portal.profile')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $request->user()->update(['password' => Hash::make($validated['password'])]);
        return Redirect::route('portal.profile')->with('status', 'password-updated');
    }
}
