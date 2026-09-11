<?php

namespace App\Http\Controllers;

use App\Models\OperatingRoom;
use App\Models\Surgery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(private readonly ReportService $reports)
    {
    }

    /**
     * Presenta el resumen de la jornada y limita la agenda al veterinario
     * autenticado cuando el usuario tiene ese rol.
     */
    public function __invoke(): View|RedirectResponse
    {
        $authenticatedUser = auth()->user();
        if ($authenticatedUser->isOwner()) {
            return redirect()->route('portal.dashboard');
        }
        $assignedVeterinarianId = $authenticatedUser->isVeterinarian()
            ? ($authenticatedUser->veterinarian?->state === 'active'
                ? $authenticatedUser->veterinarian->id
                : null)
            : null;
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');
        $period = request()->validate(['period' => ['nullable', 'in:day,week,month']])['period'] ?? 'day';
        $periodStart = match ($period) { 'week' => now()->startOfWeek(), 'month' => now()->startOfMonth(), default => now()->startOfDay() };
        $periodEnd = match ($period) { 'week' => now()->endOfWeek(), 'month' => now()->endOfMonth(), default => now()->endOfDay() };
        $periodLabel = ['day' => 'Diario', 'week' => 'Semanal', 'month' => 'Mensual'][$period];

        $report = $this->reports->build($periodStart, $periodEnd, $assignedVeterinarianId);

        $surgeryRelations = [
            'pet',
            'veterinarian',
            'operatingRoom',
            'surgeryType',
        ];

        $todaySurgeries = $report['surgeries'];

        $todaySurgeriesCount = $todaySurgeries->count();
        $scheduledSurgeriesCount = $report['scheduledSurgeries'];
        $inProgressSurgeriesCount = $report['inProgressSurgeries'];
        $completedSurgeriesCount = $report['completedSurgeries'];
        $cancelledSurgeriesCount = $report['cancelledSurgeries'];

        $pendingSurgeriesCount = $todaySurgeries
            ->whereIn('state', ['scheduled', 'in_progress'])
            ->count();

        $upcomingSurgeries = Surgery::query()
            ->with($surgeryRelations)
            ->where('state', 'scheduled')
            ->when($authenticatedUser->isVeterinarian(), fn ($query) => $query->where('veterinarian_id', $assignedVeterinarianId ?? 0))
            ->where(function ($query) use ($today, $currentTime) {
                $query
                    ->whereDate('scheduled_date', '>', $today)
                    ->orWhere(function ($query) use ($today, $currentTime) {
                        $query
                            ->whereDate('scheduled_date', $today)
                            ->whereTime('start_time', '>=', $currentTime);
                    });
            })
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $operatingRooms = OperatingRoom::query()
            ->orderBy('name')
            ->get();

        return view('dashboard', [
            'todaySurgeries' => $todaySurgeries,
            'todaySurgeriesCount' => $todaySurgeriesCount,
            'scheduledSurgeriesCount' => $scheduledSurgeriesCount,
            'inProgressSurgeriesCount' => $inProgressSurgeriesCount,
            'completedSurgeriesCount' => $completedSurgeriesCount,
            'cancelledSurgeriesCount' => $cancelledSurgeriesCount,
            'pendingSurgeriesCount' => $pendingSurgeriesCount,
            'upcomingSurgeries' => $upcomingSurgeries,
            'operatingRooms' => $operatingRooms,
            'roomsTimestamp' => now()->format('H:i'),
            'period' => $period,
            'periodLabel' => $periodLabel,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'noShowSurgeriesCount' => $report['noShowSurgeries'],
            'completionRate' => $report['completionRate'],
            'cancellationRate' => $report['cancellationRate'],
            'noShowRate' => $report['noShowRate'],
            'onTimeStartRate' => $report['onTimeStartRate'],
            'averageDuration' => $report['averageDuration'],
            'occupancyRate' => $report['occupancyRate'],
            'schedulingConflictCount' => $report['schedulingConflictCount'],
            'veterinarianConflictCount' => $report['veterinarianConflictCount'],
            'operatingRoomConflictCount' => $report['operatingRoomConflictCount'],
            'averageSchedulingLeadTimeLabel' => $report['averageSchedulingLeadTimeLabel'],
            'roomSummary' => $report['roomSummary'],
            'veterinarianSummary' => $report['veterinarianSummary'],
        ]);
    }

    public function live(Request $request)
    {
        $user = $request->user();
        abort_if($user->isOwner(), 403);
        $view = $this->__invoke();

        return response()->json(['html' => $view->render()]);
    }
}
