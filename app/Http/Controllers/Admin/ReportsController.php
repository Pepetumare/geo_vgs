<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
// Facade de DomPDF (si existe). Mantenerlo; la exportación abajo es tolerante a versión.
use Barryvdh\DomPDF\Facade\Pdf as DompdfNewFacade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $allUsers   = User::orderBy('name')->get();
        $filters    = $this->resolveFilters($request);
        $reportData = $this->buildReportData($filters);

        $chartLabels = json_encode(array_column($reportData, 'user_name'));
        $chartData   = json_encode(array_map(fn ($hours) => round($hours, 2), array_column($reportData, 'total_hours')));
        $totalHours  = array_reduce($reportData, fn ($carry, $item) => $carry + $item['total_hours'], 0);

        return view('admin.reports.index', [
            'reportData'  => $reportData,
            'allUsers'    => $allUsers,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
            'filters'     => $filters,
            'totalHours'  => $totalHours,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters    = $this->resolveFilters($request);
        $reportData = $this->buildReportData($filters);
        $totalHours = array_reduce($reportData, fn ($carry, $item) => $carry + $item['total_hours'], 0);
        $generatedAt = Carbon::now();

        // --- Exportación tolerante a versión de barryvdh/laravel-dompdf ---
        $pdfInstance = null;

        // v2.x: Barryvdh\DomPDF\Facade\Pdf
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            /** @var \Barryvdh\DomPDF\PDF $pdfInstance */
            $pdfInstance = DompdfNewFacade::loadView('admin.reports.pdf', [
                'reportData'  => $reportData,
                'filters'     => $filters,
                'totalHours'  => $totalHours,
                'generatedAt' => $generatedAt,
            ]);
        }
        // v0.8.x: Barryvdh\DomPDF\Facade (alias PDF)
        elseif (class_exists(\Barryvdh\DomPDF\Facade::class)) {
            /** @var \Barryvdh\DomPDF\PDF $pdfInstance */
            $pdfInstance = \Barryvdh\DomPDF\Facade::loadView('admin.reports.pdf', [
                'reportData'  => $reportData,
                'filters'     => $filters,
                'totalHours'  => $totalHours,
                'generatedAt' => $generatedAt,
            ]);
        }
        // Binding de contenedor (algunas instalaciones)
        elseif (app()->bound('dompdf.wrapper')) {
            /** @var \Barryvdh\DomPDF\PDF $pdfInstance */
            $pdfInstance = app('dompdf.wrapper')->loadView('admin.reports.pdf', [
                'reportData'  => $reportData,
                'filters'     => $filters,
                'totalHours'  => $totalHours,
                'generatedAt' => $generatedAt,
            ]);
        } else {
            abort(500, 'DOMPDF no está instalado/registrado en el servidor.');
        }

        $pdfInstance->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'reporte_asistencias_%s_%s.pdf',
            Carbon::parse($filters['start_date'])->format('Ymd'),
            Carbon::parse($filters['end_date'])->format('Ymd')
        );

        return $pdfInstance->download($fileName);
    }

    private function resolveFilters(Request $request): array
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
        }

        if (!$endDate) {
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        if (Carbon::parse($endDate)->lessThan(Carbon::parse($startDate))) {
            $endDate = Carbon::parse($startDate)->toDateString();
        }

        return [
            'user_id'    => $request->input('user_id'),
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ];
    }

    /**
     * Construye un arreglo por usuario con:
     * - total_hours (float)
     * - shifts_by_day: [ 'YYYY-MM-DD' => ['total_hours' => float, 'segments' => [...]] ]
     * Los segmentos se parten por día si el rango cruza medianoche.
     */
    private function buildReportData(array $filters): array
    {
        $query = User::query();
        if (!empty($filters['user_id'])) {
            $query->where('id', $filters['user_id']);
        }

        $startDate = Carbon::parse($filters['start_date'])->startOfDay();
        $endDate   = Carbon::parse($filters['end_date'])->endOfDay();

        $users = $query->with(['attendances' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->orderBy('created_at', 'asc');
        }])->get();

        $reportData = [];

        foreach ($users as $user) {
            $totalSeconds  = 0;
            $dailyTotals   = [];   // 'YYYY-MM-DD' => seconds
            $segmentsByDay = [];   // 'YYYY-MM-DD' => [ [segment], ... ]
            $currentEntry  = null;

            foreach ($user->attendances as $record) {
                if ($record->type === 'entrada' && $currentEntry === null) {
                    $currentEntry = $record;
                    continue;
                }

                if ($record->type === 'salida' && $currentEntry !== null) {
                    $start = $currentEntry->created_at->copy();
                    $end   = $record->created_at->copy();

                    if ($end->lessThanOrEqualTo($start)) {
                        // Datos inconsistentes; ignorar este par
                        $currentEntry = null;
                        continue;
                    }

                    // Partir por días si cruza medianoche
                    $segmentStart = $start->copy();
                    while ($segmentStart->lt($end)) {
                        $dayKey    = $segmentStart->toDateString();
                        $dayEnd    = $segmentStart->copy()->endOfDay();
                        $segmentEnd = $end->lessThan($dayEnd) ? $end->copy() : $dayEnd;

                        $durationSeconds = $segmentStart->diffInSeconds($segmentEnd);
                        if ($durationSeconds > 0) {
                            $dailyTotals[$dayKey] = ($dailyTotals[$dayKey] ?? 0) + $durationSeconds;

                            $segmentsByDay[$dayKey][] = [
                                'entrada'          => $currentEntry,
                                'salida'           => $record,
                                'entrada_at'       => $segmentStart->copy(),
                                'salida_at'        => $segmentEnd->copy(),
                                'duration_in_hours'=> $durationSeconds / 3600,
                            ];

                            $totalSeconds += $durationSeconds;
                        }

                        // Mover al siguiente día o al final
                        $segmentStart = $segmentEnd->copy()->addSecond();
                    }

                    $currentEntry = null;
                }
            }

            // Si quedó una entrada sin salida, puedes decidir ignorarla o contar hasta endDate.
            // Por ahora la ignoramos para no inflar horas.

            if ($totalSeconds > 0) {
                // Ordenar segmentos por hora de entrada
                $shiftsByDay = [];
                foreach ($segmentsByDay as $dayKey => $segments) {
                    usort($segments, fn ($a, $b) =>
                        $a['entrada_at']->lt($b['entrada_at']) ? -1 : ($a['entrada_at']->eq($b['entrada_at']) ? 0 : 1)
                    );

                    $shiftsByDay[$dayKey] = [
                        'total_hours' => ($dailyTotals[$dayKey] ?? 0) / 3600,
                        'segments'    => $segments,
                    ];
                }
                ksort($shiftsByDay);

                $reportData[] = [
                    'user_id'       => $user->id,
                    'user_name'     => $user->name,
                    'total_hours'   => $totalSeconds / 3600,
                    'shifts_by_day' => $shiftsByDay,
                ];
            }
        }

        return $reportData;
    }
}
