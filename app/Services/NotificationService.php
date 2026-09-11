<?php

namespace App\Services;

use App\Models\Surgery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    public function for(User $user): Collection
    {
        $veterinarianId = null;
        if ($user->isVeterinarian()) {
            $veterinarianId = $user->veterinarian?->state === 'active'
                ? $user->veterinarian->id
                : 0;
        }

        $surgeries = Surgery::query()
            ->with(['pet', 'operatingRoom', 'veterinarian'])
            ->when($user->isVeterinarian(), fn ($query) => $query->where('veterinarian_id', $veterinarianId))
            ->when($user->isOwner(), fn ($query) => $query->whereHas('pet', fn ($petQuery) => $petQuery->where('owner_id', $user->owner?->id ?? 0)))
            ->where(function ($query) {
                $query->where('state', 'in_progress')
                    ->orWhere(function ($query) {
                        $query->where('state', 'scheduled')
                            ->whereDate('scheduled_date', '>=', now()->toDateString())
                            ->whereDate('scheduled_date', '<=', now()->addDays(7)->toDateString());
                    });
            })
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        $now = now(config('app.timezone', 'America/Guayaquil'));
        $limit = $now->copy()->addDays(7);

        return $surgeries->flatMap(function (Surgery $surgery) use ($now, $limit, $user) {
            $notifications = collect();
            $pet = $surgery->pet?->name ?? 'la mascota';
            $room = $surgery->operatingRoom?->name ?? 'el quirófano asignado';

            if ($surgery->state === 'in_progress') {
                $notifications->push([
                    'key' => 'in-progress-' . $surgery->id,
                    'type' => 'in_progress',
                    'title' => 'Cirugía en curso',
                    'message' => "La cirugía de {$pet} está en curso en {$room}. Márcala como completada al finalizar.",
                    'url' => $user->isOwner() ? route('portal.dashboard') : route('surgeries.show', $surgery),
                    'date' => $surgery->scheduled_date,
                    'priority' => 'high',
                ]);
            }

            if ($surgery->state === 'scheduled') {
                $scheduledAt = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->start_time, $now->getTimezone());
                if ($scheduledAt->betweenIncluded($now, $limit)) {
                    $minutesUntil = (int) round($now->diffInMinutes($scheduledAt));
                    $when = $scheduledAt->isToday()
                        ? 'hoy a las ' . $scheduledAt->format('H:i')
                        : ($scheduledAt->isTomorrow() ? 'mañana a las ' . $scheduledAt->format('H:i') : 'el ' . $scheduledAt->format('d/m') . ' a las ' . $scheduledAt->format('H:i'));
                    $notifications->push([
                        'key' => 'upcoming-' . $surgery->id,
                        'type' => 'upcoming',
                        'title' => $minutesUntil <= 60 ? 'Cirugía inminente' : 'Cirugía próxima',
                        'message' => $minutesUntil <= 60
                            ? "La cirugía de {$pet} comienza en aproximadamente {$minutesUntil} minutos en {$room}."
                            : "La cirugía de {$pet} está programada {$when} en {$room}.",
                        'url' => $user->isOwner() ? route('portal.dashboard') : route('surgeries.show', $surgery),
                        'date' => $scheduledAt,
                        'priority' => $minutesUntil <= 60 ? 'high' : 'normal',
                    ]);
                }
            }

            return $notifications;
        })->sortByDesc(fn (array $notification) => $notification['priority'] === 'high')->values();
    }
}
