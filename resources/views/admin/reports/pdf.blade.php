@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencias</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px;
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            background-color: #f9fafb;
            font-size: 12px;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            font-weight: 600;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .meta {
            margin-top: 8px;
            color: #4b5563;
        }

        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .summary-card {
            flex: 1 1 160px;
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
        }

        .summary-card span {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
        }

        .summary-card strong {
            display: block;
            margin-top: 6px;
            font-size: 16px;
        }

        .user-card {
            margin-bottom: 24px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            page-break-inside: avoid;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #eef2ff;
            color: #4338ca;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .day-block {
            margin-top: 16px;
        }

        .day-title {
            font-size: 13px;
            color: #1f2937;
            margin-bottom: 8px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: #f9fafb;
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background: #111827;
            color: #ffffff;
        }

        th,
        td {
            padding: 10px 12px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #eef2ff;
        }

        .text-muted {
            color: #6b7280;
        }

        .alert {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 9999px;
            background: #fef3c7;
            color: #92400e;
            font-weight: 600;
        }

        .empty-state {
            margin-top: 40px;
            text-align: center;
            padding: 40px;
            border: 2px dashed #d1d5db;
            border-radius: 16px;
            color: #6b7280;
            background: #ffffff;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Asistencias</h1>
        <p class="meta">Periodo del {{ Carbon::parse($filters['start_date'])->format('d/m/Y') }} al
            {{ Carbon::parse($filters['end_date'])->format('d/m/Y') }}</p>
        <p class="meta">Generado el {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <span>Colaboradores</span>
            <strong>{{ count($reportData) }}</strong>
        </div>
        <div class="summary-card">
            <span>Total horas del período</span>
            <strong>{{ number_format($totalHours, 2) }} h</strong>
        </div>
        <div class="summary-card">
            <span>Rango seleccionado</span>
            <strong>{{ Carbon::parse($filters['start_date'])->format('d M Y') }} —
                {{ Carbon::parse($filters['end_date'])->format('d M Y') }}</strong>
        </div>
    </div>

    @if (count($reportData) === 0)
        <div class="empty-state">
            <h3>No se encontraron registros para los filtros seleccionados.</h3>
            <p class="text-muted">Intenta ajustar el rango de fechas o seleccionar otro colaborador.</p>
        </div>
    @else
        @foreach ($reportData as $data)
            <div class="user-card">
                <div class="user-header">
                    <div>
                        <h2>{{ $data['user_name'] }}</h2>
                        <p class="text-muted">Total en el período: {{ number_format($data['total_hours'], 2) }} h</p>
                    </div>
                    <span class="badge">{{ number_format($data['total_hours'], 2) }} h</span>
                </div>

                @foreach ($data['shifts_by_day'] as $day => $dayData)
                    <div class="day-block">
                        <div class="day-title">{{ Carbon::parse($day)->format('d/m/Y') }} · Total del día:
                            {{ number_format($dayData['total_hours'], 2) }} h</div>
=======
                @foreach ($data['shifts_by_day'] as $day => $shifts)
                    <div class="day-block">
                        <div class="day-title">{{ Carbon::parse($day)->format('d/m/Y') }} · Total del día:
                            {{ number_format(array_sum(array_column($shifts, 'duration_in_hours')), 2) }} h</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Entrada</th>
                                    <th>Ubicación entrada</th>
                                    <th>Salida</th>
                                    <th>Ubicación salida</th>
                                    <th>Duración</th>
                                    <th>Alertas</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($dayData['segments'] as $shift)
                                    <tr>
                                        <td>{{ $shift['entrada_at']->format('H:i:s') }}</td>
                                        <td class="text-muted">{{ $shift['entrada']->latitude }},
                                            {{ $shift['entrada']->longitude }}</td>
                                        <td>{{ $shift['salida_at']->format('H:i:s') }}</td>
                                @foreach ($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift['entrada']->created_at->format('H:i:s') }}</td>
                                        <td class="text-muted">{{ $shift['entrada']->latitude }},
                                            {{ $shift['entrada']->longitude }}</td>
                                        <td>{{ $shift['salida']->created_at->format('H:i:s') }}</td>
                                        <td class="text-muted">{{ $shift['salida']->latitude }},
                                            {{ $shift['salida']->longitude }}</td>
                                        <td>{{ number_format($shift['duration_in_hours'], 2) }} h</td>
                                        <td>
                                            @if ($shift['entrada']->is_suspicious)
                                                <span class="alert">⚠ Posible simulación</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

    <div class="footer">
        Reporte generado automáticamente por el panel administrativo · {{ $generatedAt->format('d/m/Y H:i') }}
    </div>
</body>

</html>
