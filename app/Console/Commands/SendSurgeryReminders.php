<?php

namespace App\Console\Commands;

use App\Models\Surgery;
use App\Services\SurgeryEmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendSurgeryReminders extends Command
{
    protected $signature = 'surgeries:send-reminders {--dry-run : Solo muestra los recordatorios pendientes}';
    protected $description = 'Envía recordatorios por correo de cirugías próximas';

    public function handle(): int
    {
        $now = Carbon::now(config('app.timezone', 'America/Guayaquil'));
        $until = $now->copy()->addHours(24);
        $sent = 0;

        Surgery::query()->with(['pet.owner', 'veterinarian.user', 'surgeryType'])
            ->where('state', 'scheduled')
            ->whereBetween('scheduled_date', [$now->toDateString(), $until->toDateString()])
            ->get()->each(function (Surgery $surgery) use ($now, $until, &$sent) {
                $scheduledAt = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->start_time, $now->getTimezone());
                if (!$scheduledAt->betweenIncluded($now, $until)) return;

                $emails = collect([$surgery->pet?->owner?->email, $surgery->veterinarian?->user?->email ?? $surgery->veterinarian?->email])
                    ->filter()->unique();
                foreach ($emails as $email) {
                    $exists = DB::table('surgery_email_logs')->where(['surgery_id' => $surgery->id, 'recipient_email' => $email, 'notification_type' => 'reminder'])->exists();
                    if ($exists) continue;
                    if ($this->option('dry-run')) { $this->line("Recordatorio: cirugía #{$surgery->id} → {$email}"); continue; }
                    if (app(SurgeryEmailService::class)->notifyParticipant($surgery, 'reminder', $email)) {
                        DB::table('surgery_email_logs')->insert(['surgery_id' => $surgery->id, 'recipient_email' => $email, 'notification_type' => 'reminder', 'sent_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
                        $sent++;
                    }
                }
            });

        $this->info("Recordatorios enviados: {$sent}.");
        return self::SUCCESS;
    }
}
