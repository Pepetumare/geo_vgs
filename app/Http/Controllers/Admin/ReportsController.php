<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = User::orderBy('name')->get();
        $filters = $this->resolveFilters($request);
        $reportData = $this->buildReportData($filters);

        $chartLabels = json_encode(array_column($reportData, 'user_name'));
        $chartData = json_encode(array_map(fn ($hours) => round($hours, 2), array_column($reportData, 'total_hours')));
        $totalHours = array_reduce($reportData, fn ($carry, $item) => $carry + $item['total_hours'], 0);

        return view('admin.reports.index', [
            'reportData' => $reportData,
            'allUsers' => $allUsers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'filters' => $filters,
            'totalHours' => $totalHours,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $reportData = $this->buildReportData($filters);
        $totalHours = array_reduce($reportData, fn ($carry, $item) => $carry + $item['total_hours'], 0);
        $generatedAt = Carbon::now();

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'reportData' => $reportData,
            'filters' => $filters,
            'totalHours' => $totalHours,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'reporte_asistencias_%s_%s.pdf',
            Carbon::parse($filters['start_date'])->format('Ymd'),
            Carbon::parse($filters['end_date'])->format('Ymd')
        );

        return $pdf->download($fileName);
    }

    private function resolveFilters(Request $request): array
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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
            'user_id' => $request->input('user_id'),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    private function buildReportData(array $filters): array
    {
        $query = User::query();
        if ($filters['user_id']) {
            $query->where('id', $filters['user_id']);
        }

        $endDate = Carbon::parse($filters['end_date'])->endOfDay();

        $users = $query->with(['attendances' => function ($q) use ($filters, $endDate) {
            $q->whereBetween('created_at', [$filters['start_date'], $endDate])
                ->orderBy('created_at', 'asc');
        }])->get();

        $reportData = [];
        foreach ($users as $user) {
            $totalSeconds = 0;
            $shifts = [];
            $currentEntry = null;

            foreach ($user->attendances as $record) {
                if ($record->type === 'entrada' && is_null($currentEntry)) {
                    $currentEntry = $record;
                } elseif ($record->type === 'salida' && !is_null($currentEntry)) {
                    $duration = $currentEntry->created_at->diffInSeconds($record->created_at);
                    $totalSeconds += $duration;

                    $shifts[$currentEntry->created_at->toDateString()][] = [
                        'entrada' => $currentEntry,
                        'salida' => $record,
                        'duration_in_hours' => $duration / 3600,
                    ];

                    $currentEntry = null;
                }
            }

            if (count($shifts) > 0) {
                $reportData[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_hours' => $totalSeconds / 3600,
                    'shifts_by_day' => $shifts,
                ];
            }
        }

        return $reportData;
    }
}
