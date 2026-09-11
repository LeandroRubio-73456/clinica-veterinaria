<?php

namespace App\Notifications;

use App\Models\Surgery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurgeryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Surgery $surgery,
        public string $event = 'scheduled',
        public ?string $actionUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->surgery->loadMissing(['pet.owner', 'surgeryType', 'veterinarian']);
        $pet = $this->surgery->pet?->name ?? 'la mascota';
        $eventLabels = [
            'scheduled' => 'Cirugía programada',
            'updated' => 'Cirugía actualizada',
            'cancelled' => 'Cirugía cancelada',
            'no_show' => 'Cirugía marcada como no presentada',
            'reminder' => 'Recordatorio de cirugía',
        ];
        $stateLabels = [
            'scheduled' => 'Programada',
            'in_progress' => 'En curso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No se presentó',
        ];

        return (new MailMessage)
            ->subject(($eventLabels[$this->event] ?? 'Actualización de cirugía') . " — {$pet}")
            ->greeting('Clínica Veterinaria')
            ->line($eventLabels[$this->event] ?? 'Se actualizó una cirugía.')
            ->line("Mascota: {$pet}")
            ->line('Procedimiento: ' . ($this->surgery->surgeryType?->name ?? 'No especificado'))
            ->line("Fecha: {$this->surgery->scheduled_date}")
            ->line("Horario: {$this->surgery->start_time} — {$this->surgery->end_time}")
            ->line('Estado actual: ' . ($stateLabels[$this->surgery->state] ?? 'No especificado'))
            ->when(in_array($this->event, ['cancelled', 'no_show'], true), fn ($mail) => $mail->line('Motivo: ' . ($this->surgery->notes ?: 'No especificado.')))
            ->action('Ver cirugía en el sistema', $this->actionUrl ?? route('surgeries.show', $this->surgery))
            ->line('Este mensaje también está disponible en el sistema de la clínica.')
            ->salutation('Atentamente, Clínica Veterinaria del Municipio');
    }
}
