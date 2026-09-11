<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecimiento de contraseña — Clínica Veterinaria del Municipio')
            ->greeting('Hola, ' . ($notifiable->name ?? ''))
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.users.expire', 60) . ' minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.')
            ->salutation('Atentamente, Clínica Veterinaria del Municipio');
    }
}
