<?php

namespace App\Console\Commands;

use App\Models\Surgery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SurgeryEmailService;

class MarkAbsentSurgeries extends Command
{
    protected $signature = 'surgeries:mark-no-show {--dry-run : Solo muestra las cirugías candidatas, sin cambiar estados}';

    protected $description = 'Marca como no presentadas las cirugías que superaron su tolerancia de llegada';

    public function handle(): int
    {
        $graceMinutes = max(15, (int) config('surgeries.no_show_grace_minutes', 15));
        $timezone = config('app.timezone', 'America/Guayaquil');
        $cutoff = Carbon::now($timezone)->subMinutes($graceMinutes);
        $markedCount = 0;
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->line('Zona horaria: ' . $timezone);
            $this->line('Tolerancia: ' . $graceMinutes . ' minutos');
            $this->line('Se marcarían como no presentadas hasta: ' . $cutoff->toDateTimeString());
        }

        Surgery::query()
            ->where('state', 'scheduled')
            ->whereDate('scheduled_date', '<=', $cutoff->toDateString())
            ->chunkById(100, function ($surgeries) use ($cutoff, $timezone, &$markedCount, $dryRun) {
                foreach ($surgeries as $surgery) {
                    $scheduledAt = Carbon::parse(
                        $surgery->scheduled_date . ' ' . $surgery->start_time,
                        $timezone
                    );

                    if ($scheduledAt->greaterThan($cutoff)) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("Candidata: cirugía #{$surgery->id} programada para {$scheduledAt->toDateTimeString()}");
                        continue;
                    }

                    $updated = DB::transaction(function () use ($surgery, $timezone) {
                        $updated = Surgery::query()
                            ->whereKey($surgery->getKey())
                            ->where('state', 'scheduled')
                            ->update([
                                'state' => 'no_show',
                                'updated_at' => Carbon::now($timezone),
                            ]);

                        if ($updated) {
                            $surgery->operatingRoom()->update([
                                'state' => 'available',
                            ]);
                        }

                        return $updated;
                    });

                    $markedCount += $updated;
                    if ($updated) {
                        app(SurgeryEmailService::class)->notifyParticipants($surgery->fresh(), 'no_show');
                    }
                }
            });

        $this->info("Cirugías marcadas como no presentadas: {$markedCount}.");

        return self::SUCCESS;
    }
}
