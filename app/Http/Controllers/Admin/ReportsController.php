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
        // --- 1. OBTENER FILTROS Y USUARIOS ---
        $allUsers = User::orderBy('name')->get();
        $userId = $request->input('user_id');
        
        // Establecer fechas por defecto al mes actual si no se proporcionan
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // --- 2. CONSTRUIR LA CONSULTA ---
        $query = User::query();

        if ($userId) {
            $query->where('id', $userId);
        }

        // Cargar las asistencias dentro del rango de fechas, ordenadas cronológicamente
        $users = $query->with(['attendances' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
              ->orderBy('created_at', 'asc');
        }])->get();

        // --- 3. PROCESAR DATOS Y CALCULAR HORAS ---
        $reportData = [];
        foreach ($users as $user) {
            $totalSeconds = 0;
            $shifts = [];
            $currentEntry = null;

            foreach ($user->attendances as $record) {
                if ($record->type == 'entrada' && is_null($currentEntry)) {
                    $currentEntry = $record;
                } elseif ($record->type == 'salida' && !is_null($currentEntry)) {
                    // Pareja encontrada: calcular duración
                    $duration = $currentEntry->created_at->diffInSeconds($record->created_at);
                    $totalSeconds += $duration;
                    
                    $shifts[ $currentEntry->created_at->toDateString() ][] = [
                        'entrada' => $currentEntry->created_at,
                        'salida' => $record->created_at,
                        'duration_in_hours' => $duration / 3600,
                    ];
                    
                    $currentEntry = null; // Resetear para el próximo turno
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
        
        // --- 4. PREPARAR DATOS PARA GRÁFICOS ---
        $chartLabels = json_encode(array_column($reportData, 'user_name'));
        $chartData = json_encode(array_column($reportData, 'total_hours'));

        return view('admin.reports.index', [
            'reportData' => $reportData,
            'allUsers' => $allUsers,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'filters' => $request->only(['user_id', 'start_date', 'end_date']),
        ]);
    }
}