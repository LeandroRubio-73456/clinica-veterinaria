<?php

use App\Models\Surgery;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\SurgeryNotification;

test('la notificación de cirugía incluye el estado actual en español', function () {
    $surgery = Surgery::factory()->create([
        'state' => 'in_progress',
    ]);

    $mail = (new SurgeryNotification($surgery, 'updated'))->toMail(User::factory()->make());

    expect($mail->subject)->toContain('Cirugía actualizada')
        ->and($mail->introLines)->toContain('Estado actual: En curso')
        ->and($mail->salutation)->toBe('Atentamente, Clínica Veterinaria del Municipio')
        ->and($mail->actionText)->toBe('Ver cirugía en el sistema')
        ->and($mail->actionUrl)->toContain('/surgeries/' . $surgery->id)
        ->and(implode(' ', $mail->introLines))->not->toContain('Regards')
        ->and(implode(' ', $mail->introLines))->not->toContain('Laravel');
});

test('la notificación de recuperación está en español y usa la clínica como remitente', function () {
    $user = User::factory()->make([
        'name' => 'María González',
        'email' => 'maria@example.test',
    ]);

    $mail = (new ResetPasswordNotification('token-de-prueba'))->toMail($user);

    expect($mail->subject)->toContain('Restablecimiento de contraseña')
        ->and($mail->greeting)->toBe('Hola, María González')
        ->and($mail->salutation)->toBe('Atentamente, Clínica Veterinaria del Municipio')
        ->and(implode(' ', $mail->introLines))->not->toContain('Regards')
        ->and(implode(' ', $mail->introLines))->not->toContain('Laravel');
});
