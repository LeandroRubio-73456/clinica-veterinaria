<?php

namespace App\Services;

use App\Models\Surgery;
use App\Notifications\SurgeryNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SurgeryEmailService
{
    public function notifyParticipants(Surgery $surgery, string $event): void
    {
        $surgery->loadMissing(['pet.owner', 'veterinarian.user']);
        $recipients = collect([
            $surgery->pet?->owner?->email,
            $surgery->veterinarian?->user?->email ?? $surgery->veterinarian?->email,
        ])->filter()->unique()->values();

        foreach ($recipients as $email) {
            $this->notifyParticipant($surgery, $event, $email);
        }
    }

    public function notifyParticipant(Surgery $surgery, string $event, string $email): bool
    {
        $surgery->loadMissing(['pet.owner', 'veterinarian.user']);
        $ownerEmail = $surgery->pet?->owner?->email;

        try {
            $url = $email === $ownerEmail
                ? route('portal.surgery', $surgery)
                : route('surgeries.show', $surgery);

            Notification::route('mail', $email)->notify(new SurgeryNotification($surgery, $event, $url));

            return true;
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar una notificación de cirugía.', [
                'surgery_id' => $surgery->id,
                'recipient' => $email,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
