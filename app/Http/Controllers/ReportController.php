<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    /**
     * Muestra indicadores del período seleccionado y su comparación relativa
     * con el período inmediatamente anterior de igual duración.
     */
    public function index(Request $request): View
    {
        [$from, $to, $period] = $this->period($request);
        $current = $this->reports->build($from, $to);
        $previousTo = $from->copy()->subDay()->endOfDay();
        $days = $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1;
        $previousFrom = $previousTo->copy()->subDays($days - 1)->startOfDay();
        $previous = $this->reports->build($previousFrom, $previousTo);

        return view('reports.index', array_merge($current, [
            'period' => $period,
            'previousFrom' => $previousFrom,
            'previousTo' => $previousTo,
            'comparison' => $this->comparison($current, $previous),
        ]));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        [$from, $to] = $this->period($request);
        $report = $this->reports->build($from, $to);
        $filename = 'reporte-cirugias-' . $from->format('Y-m-d') . '-a-' . $to->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Indicador', 'Valor'], ';');
            fputcsv($handle, ['Período', $report['from']->format('d/m/Y') . ' - ' . $report['to']->format('d/m/Y')], ';');
            fputcsv($handle, ['Cirugías registradas', $report['totalSurgeries']], ';');
            fputcsv($handle, ['Completadas', $report['completedSurgeries']], ';');
            fputcsv($handle, ['Canceladas', $report['cancelledSurgeries']], ';');
            fputcsv($handle, ['No presentadas', $report['noShowSurgeries']], ';');
            fputcsv($handle, ['Tasa de finalización', $report['completionRate'] . '%'], ';');
            fputcsv($handle, ['Tasa de cancelación', $report['cancellationRate'] . '%'], ';');
            fputcsv($handle, ['Tasa de no presentación', $report['noShowRate'] . '%'], ';');
            fputcsv($handle, ['Inicio a tiempo', ($report['onTimeStartRate'] ?? '—') . '%'], ';');
            fputcsv($handle, ['Tiempo registro a programación', $report['averageSchedulingLeadTimeLabel']], ';');
            fputcsv($handle, ['Ocupación global de quirófanos', $report['occupancyRate'] . '%'], ';');
            fputcsv($handle, ['Intentos de programación rechazados', $report['schedulingConflictCount']], ';');
            fputcsv($handle, ['Conflictos de veterinario', $report['veterinarianConflictCount']], ';');
            fputcsv($handle, ['Conflictos de quirófano', $report['operatingRoomConflictCount']], ';');
            fputcsv($handle, [], ';');
            fputcsv($handle, ['Quirófano', 'Minutos ocupados', 'Minutos disponibles', 'Utilización', 'Cirugías', 'Completadas'], ';');
            foreach ($report['roomSummary'] as $room) {
                fputcsv($handle, [$room['name'], $room['occupied_minutes'], $room['available_minutes'], $room['occupancy_rate'] . '%', $room['total'], $room['completed']], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf(Request $request): View
    {
        [$from, $to] = $this->period($request);
        return view('reports.pdf', ['report' => $this->reports->build($from, $to)]);
    }

    private function period(Request $request): array
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:day,week,month,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $period = $validated['period'] ?? (($validated['from'] ?? null) || ($validated['to'] ?? null) ? 'custom' : 'month');
        $today = now();

        return match ($period) {
            'day' => [$today->copy()->startOfDay(), $today->copy()->endOfDay(), $period],
            'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek(), $period],
            'custom' => [
                Carbon::parse($validated['from'] ?? $today->copy()->startOfMonth()->toDateString())->startOfDay(),
                Carbon::parse($validated['to'] ?? $today->copy()->endOfMonth()->toDateString())->endOfDay(),
                $period,
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth(), 'month'],
        };
    }

    private function comparison(array $current, array $previous): array
    {
        return [
            'total' => $this->delta($current['totalSurgeries'], $previous['totalSurgeries']),
            'completion' => $this->delta($current['completionRate'], $previous['completionRate']),
            'cancellation' => $this->delta($current['cancellationRate'], $previous['cancellationRate']),
            'no_show' => $this->delta($current['noShowRate'], $previous['noShowRate']),
            'occupancy' => $this->delta($current['occupancyRate'], $previous['occupancyRate']),
            'conflicts' => $this->delta($current['schedulingConflictCount'], $previous['schedulingConflictCount']),
        ];
    }

    private function delta(float|int $current, float|int $previous): ?float
    {
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : null;
    }
}
