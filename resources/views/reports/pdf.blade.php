<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de cirugías {{ $report['from']->format('d-m-Y') }} a {{ $report['to']->format('d-m-Y') }}</title>
    <style>
        @page { margin: 18mm; }
        body { color: #1f2933; font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        h1 { margin: 0 0 4px; font-size: 24px; }
        h2 { margin: 24px 0 8px; font-size: 16px; border-bottom: 1px solid #d9e2e8; padding-bottom: 5px; }
        .muted { color: #667781; }
        .metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .metric { border: 1px solid #d9e2e8; padding: 10px; border-radius: 5px; }
        .metric strong { display: block; font-size: 19px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #e4eaee; padding: 7px 6px; text-align: left; }
        th { background: #f4f7f8; font-size: 10px; text-transform: uppercase; }
        .toolbar { margin-bottom: 18px; }
        button { background: #0f766e; border: 0; border-radius: 4px; color: white; cursor: pointer; padding: 8px 14px; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Imprimir / Guardar como PDF</button></div>
    <h1>Reporte de gestión quirúrgica</h1>
    <p class="muted">Período: {{ $report['from']->format('d/m/Y') }} — {{ $report['to']->format('d/m/Y') }}</p>

    <div class="metrics">
        <div class="metric">Cirugías registradas<strong>{{ $report['totalSurgeries'] }}</strong></div>
        <div class="metric">Tasa de finalización<strong>{{ $report['completionRate'] }}%</strong></div>
        <div class="metric">Ocupación de quirófanos<strong>{{ $report['occupancyRate'] }}%</strong></div>
        <div class="metric">Inicio a tiempo<strong>{{ $report['onTimeStartRate'] === null ? '—' : $report['onTimeStartRate'] . '%' }}</strong></div>
    </div>

    <h2>Resultados operativos</h2>
    <table>
        <tbody>
            <tr><th>Completadas</th><td>{{ $report['completedSurgeries'] }}</td><th>Canceladas</th><td>{{ $report['cancelledSurgeries'] }}</td></tr>
            <tr><th>No presentadas</th><td>{{ $report['noShowSurgeries'] }}</td><th>Tiempo registro a programación</th><td>{{ $report['averageSchedulingLeadTimeLabel'] }}</td></tr>
            <tr><th>Minutos ocupados</th><td>{{ number_format($report['totalOccupiedMinutes']) }}</td><th>Minutos disponibles</th><td>{{ number_format($report['totalAvailableMinutes']) }}</td></tr>
        </tbody>
    </table>

    <h2>Ocupación por quirófano</h2>
    <table>
        <thead><tr><th>Quirófano</th><th>Minutos ocupados</th><th>Minutos disponibles</th><th>Utilización</th><th>Cirugías</th></tr></thead>
        <tbody>
            @forelse ($report['roomSummary'] as $room)
                <tr><td>{{ $room['name'] }}</td><td>{{ number_format($room['occupied_minutes']) }}</td><td>{{ number_format($room['available_minutes']) }}</td><td>{{ $room['occupancy_rate'] }}%</td><td>{{ $room['total'] }}</td></tr>
            @empty
                <tr><td colspan="5">No hay quirófanos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Resumen diario</h2>
    <table>
        <thead><tr><th>Fecha</th><th>Total</th><th>Completadas</th><th>Canceladas</th><th>No presentadas</th></tr></thead>
        <tbody>
            @forelse ($report['dailySummary'] as $row)
                <tr>
                    <td>{{ $row['date']->format('d/m/Y') }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ data_get($row['states']->get('completed'), 'count', 0) }}</td>
                    <td>{{ data_get($row['states']->get('cancelled'), 'count', 0) }}</td>
                    <td>{{ data_get($row['states']->get('no_show'), 'count', 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No hay cirugías en el período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="muted" style="margin-top: 24px;">Horario operativo utilizado: {{ $report['operatingStart'] }}–{{ $report['operatingEnd'] }}. El porcentaje de ocupación usa minutos reales de cirugías en curso o completadas.</p>
</body>
</html>
