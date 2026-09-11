<?php

namespace App\Services;

use App\Models\OperatingRoom;
use App\Models\SchedulingConflict;
use App\Models\Surgery;
use Carbon\Carbon;

class ReportService
{
    /**
     * Construye todos los indicadores operativos para un período.
     *
     * `created_at` se utiliza como momento de registro de la cirugía. Mientras
     * no exista un campo `requested_at`, el resultado representa tiempo de
     * programación administrativa y no el tiempo clínico total de espera.
     */
    public function build(Carbon $from, Carbon $to, ?int $veterinarianId = null): array
    {
        $timezone = config('app.timezone', 'America/Guayaquil');
        $surgeries = Surgery::query()
            ->with(['pet', 'veterinarian', 'operatingRoom', 'surgeryType'])
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->when($veterinarianId, fn ($query) => $query->where('veterinarian_id', $veterinarianId))
            ->orderBy('scheduled_date')->orderBy('start_time')->get();

        $total = $surgeries->count();
        $completed = $surgeries->where('state', 'completed')->count();
        $scheduled = $surgeries->where('state', 'scheduled')->count();
        $inProgress = $surgeries->where('state', 'in_progress')->count();
        $cancelled = $surgeries->where('state', 'cancelled')->count();
        $noShow = $surgeries->where('state', 'no_show')->count();

        $schedulingConflicts = SchedulingConflict::query()
            ->whereBetween('scheduled_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $durations = $surgeries->where('state', 'completed')
            ->filter(fn (Surgery $surgery) => $surgery->actual_start_time && $surgery->actual_end_time)
            ->map(function (Surgery $surgery) {
                $start = Carbon::parse($surgery->actual_start_time);
                $end = Carbon::parse($surgery->actual_end_time);
                return $end->greaterThanOrEqualTo($start) ? $start->diffInMinutes($end) : null;
            })->filter(fn (?int $minutes) => $minutes !== null);

        $leadTimes = $surgeries->filter(fn (Surgery $surgery) => $surgery->created_at && $surgery->scheduled_date && $surgery->start_time)
            ->map(function (Surgery $surgery) use ($timezone) {
                $created = Carbon::parse($surgery->created_at)->setTimezone($timezone);
                $scheduled = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->start_time, $timezone);
                return $scheduled->greaterThanOrEqualTo($created) ? $created->diffInMinutes($scheduled) : null;
            })->filter(fn (?int $minutes) => $minutes !== null);

        $onTime = $surgeries->filter(fn (Surgery $surgery) => in_array($surgery->state, ['completed', 'in_progress'], true) && $surgery->actual_start_time)
            ->map(function (Surgery $surgery) use ($timezone) {
                $scheduled = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->start_time, $timezone);
                $actual = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->actual_start_time, $timezone);
                return $actual->lessThanOrEqualTo($scheduled->copy()->addMinutes(config('clinic.on_time_grace_minutes', 15)));
            });

        $operatingStart = config('clinic.operating_hours.start', '07:00');
        $operatingEnd = config('clinic.operating_hours.end', '19:00');
        $operatingDays = config('clinic.operating_hours.days', [1, 2, 3, 4, 5]);
        $dailyCapacity = Carbon::parse($operatingStart, $timezone)->diffInMinutes(Carbon::parse($operatingEnd, $timezone));
        $operatingDaysInPeriod = 0;
        for ($day = $from->copy()->startOfDay(); $day->lte($to); $day->addDay()) {
            if (in_array($day->isoWeekday(), $operatingDays, true)) $operatingDaysInPeriod++;
        }

        // La capacidad debe representar únicamente quirófanos operativos.
        // Un quirófano inactivo o en mantenimiento no debe diluir la ocupación.
        $rooms = OperatingRoom::query()
            ->whereNotIn('state', ['inactive', 'maintenance'])
            ->orderBy('name')
            ->get();
        $availablePerRoom = $operatingDaysInPeriod * $dailyCapacity;
        $occupiedByRoom = $surgeries->groupBy('operating_room_id')->map(fn ($items) => $items->sum(fn (Surgery $surgery) => $this->occupiedMinutes($surgery, $from, $to, $operatingDays, $operatingStart, $operatingEnd, $timezone)));
        $roomSummary = $rooms->map(function (OperatingRoom $room) use ($surgeries, $occupiedByRoom, $availablePerRoom) {
            $items = $surgeries->where('operating_room_id', $room->id);
            $occupied = (int) $occupiedByRoom->get($room->id, 0);
            return [
                'name' => $room->name,
                'total' => $items->count(),
                'completed' => $items->where('state', 'completed')->count(),
                'occupied_minutes' => $occupied,
                'available_minutes' => $availablePerRoom,
                'occupancy_rate' => $availablePerRoom > 0 ? round(($occupied / $availablePerRoom) * 100, 1) : 0,
            ];
        })->sortByDesc('occupancy_rate')->values();

        $totalAvailable = $roomSummary->sum('available_minutes');
        $totalOccupied = $roomSummary->sum('occupied_minutes');

        return [
            'from' => $from, 'to' => $to, 'surgeries' => $surgeries,
            'totalSurgeries' => $total, 'scheduledSurgeries' => $scheduled, 'inProgressSurgeries' => $inProgress,
            'completedSurgeries' => $completed, 'cancelledSurgeries' => $cancelled, 'noShowSurgeries' => $noShow,
            'schedulingConflictCount' => $schedulingConflicts->count(),
            'veterinarianConflictCount' => $schedulingConflicts->where('conflict_type', 'veterinarian')->count(),
            'operatingRoomConflictCount' => $schedulingConflicts->where('conflict_type', 'operating_room')->count(),
            'completionRate' => $this->rate($completed, $total), 'cancellationRate' => $this->rate($cancelled, $total), 'noShowRate' => $this->rate($noShow, $total),
            'averageDuration' => $durations->isNotEmpty() ? round($durations->average()) : null,
            'averageSchedulingLeadTimeLabel' => $this->formatMinutes($leadTimes->isNotEmpty() ? round($leadTimes->average()) : null),
            'schedulingLeadTimesCount' => $leadTimes->count(),
            'onTimeStartRate' => $onTime->isNotEmpty() ? round(($onTime->filter()->count() / $onTime->count()) * 100, 1) : null,
            'onTimeStartCount' => $onTime->count(),
            'operatingStart' => $operatingStart, 'operatingEnd' => $operatingEnd,
            'operatingDaysInPeriod' => $operatingDaysInPeriod, 'operatingDayMinutes' => $dailyCapacity,
            'availableMinutesPerRoom' => $availablePerRoom, 'totalAvailableMinutes' => $totalAvailable, 'totalOccupiedMinutes' => $totalOccupied,
            'occupancyRate' => $totalAvailable > 0 ? round(($totalOccupied / $totalAvailable) * 100, 1) : 0,
            'dailySummary' => $this->dailySummary($surgeries), 'roomSummary' => $roomSummary, 'veterinarianSummary' => $this->veterinarianSummary($surgeries),
        ];
    }

    private function dailySummary($surgeries)
    {
        return $surgeries->groupBy(fn (Surgery $surgery) => Carbon::parse($surgery->scheduled_date)->toDateString())->map(function ($items, $date) {
            return ['date' => Carbon::parse($date), 'total' => $items->count(), 'states' => $items->groupBy('state')->map(fn ($stateItems) => ['count' => $stateItems->count(), 'surgeries' => $stateItems->values()])->sortKeys()];
        })->values();
    }

    private function veterinarianSummary($surgeries)
    {
        return $surgeries->groupBy(fn (Surgery $surgery) => trim(($surgery->veterinarian?->first_name ?? '') . ' ' . ($surgery->veterinarian?->last_name ?? '')) ?: 'Sin veterinario')->map(fn ($items, $name) => ['name' => $name, 'total' => $items->count(), 'completed' => $items->where('state', 'completed')->count()])->sortByDesc('total')->values();
    }

    private function rate(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }

    private function formatMinutes(?int $minutes): string
    {
        if ($minutes === null) return '—';
        if ($minutes < 60) return $minutes . ' min';
        $days = intdiv($minutes, 1440); $hours = intdiv($minutes % 1440, 60); $remaining = $minutes % 60; $parts = [];
        if ($days > 0) $parts[] = $days . ' d';
        if ($hours > 0) $parts[] = $hours . ' h';
        if ($remaining > 0 && $days === 0) $parts[] = $remaining . ' min';
        return implode(' ', $parts);
    }

    private function occupiedMinutes(Surgery $surgery, Carbon $from, Carbon $to, array $days, string $open, string $close, string $timezone): int
    {
        if (! in_array($surgery->state, ['completed', 'in_progress'], true) || ! $surgery->actual_start_time) return 0;
        $start = Carbon::parse($surgery->scheduled_date . ' ' . $surgery->actual_start_time, $timezone);
        $end = $surgery->actual_end_time ? Carbon::parse($surgery->scheduled_date . ' ' . $surgery->actual_end_time, $timezone) : now($timezone);
        if ($end->lessThanOrEqualTo($start)) return 0;
        $start = $start->greaterThan($from) ? $start : $from->copy();
        $end = $end->lessThan($to) ? $end : $to->copy();
        if ($end->lessThanOrEqualTo($start)) return 0;
        $minutes = 0;
        for ($day = $start->copy()->startOfDay(); $day->lte($end); $day->addDay()) {
            if (! in_array($day->isoWeekday(), $days, true)) continue;
            $opening = Carbon::parse($day->toDateString() . ' ' . $open, $timezone); $closing = Carbon::parse($day->toDateString() . ' ' . $close, $timezone);
            $overlapStart = $start->greaterThan($opening) ? $start : $opening; $overlapEnd = $end->lessThan($closing) ? $end : $closing;
            if ($overlapEnd->greaterThan($overlapStart)) $minutes += $overlapStart->diffInMinutes($overlapEnd);
        }
        return $minutes;
    }
}
