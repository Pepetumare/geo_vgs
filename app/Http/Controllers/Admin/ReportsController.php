<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = User::orderBy('name')->get();
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = User::query();
        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->with(['attendances' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'asc');
        }])->get();

        $reportData = [];
        foreach ($users as $user) {
            $totalSeconds = 0;
            $shifts = [];
            $currentEntry = null;

            foreach ($user->attendances as $record) {
                if ($record->type == 'entrada' && is_null($currentEntry)) {
                    $currentEntry = $record;
                } elseif ($record->type == 'salida' && !is_null($currentEntry)) {
                    $duration = $currentEntry->created_at->diffInSeconds($record->created_at);
                    $totalSeconds += $duration;

                    // --- CAMBIO CLAVE AQUÍ ---
                    // Guardamos el objeto completo, no solo la fecha.
                    $shifts[$currentEntry->created_at->toDateString()][] = [
                        'entrada' => $currentEntry, // Objeto completo
                        'salida' => $record,       // Objeto completo
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

        $chartLabels = json_encode(array_column($reportData, 'user_name'));
        $chartData = json_encode(array_column($reportData, 'total_hours'));

        return view('admin.reports.index', [
            'reportData' => $reportData,
            'allUsers' => $allUsers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'filters' => [
                'user_id' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
