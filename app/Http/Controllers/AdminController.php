<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos todos los usuarios para el menú desplegable del filtro.
        $allUsers = User::orderBy('name')->get();

        // Empezamos a construir la consulta de usuarios.
        $query = User::query();

        // Si se seleccionó un empleado específico, filtramos por él.
        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        // Cargamos las asistencias, aplicando el filtro de fecha si se proporcionó.
        $query->whereHas('attendances')->with(['attendances' => function ($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->whereDate('created_at', '<=', $request->end_date);
            }
            $q->orderBy('id', 'desc');
        }]);

        $usersWithAttendances = $query->get();

        return view('admin.dashboard', [
            'users' => $usersWithAttendances,
            'allUsers' => $allUsers,
            'filters' => $request->only(['user_id', 'start_date', 'end_date']), // Pasamos los filtros a la vista
        ]);
    }

    public function getCalendarData(User $user, $year, $month)
    {
        // Validar que el mes y año sean correctos
        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Mes inválido'], 400);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Obtener los días en que el usuario SÍ trabajó (asistencia)
        $attendances = $user->attendances()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        // 2. Obtener los feriados del mes
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $calendarData = [];

        // 3. Recorrer cada día del mes para determinar su estado
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $currentDateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek; // Domingo = 0, Sábado = 6

            if (isset($holidays[$currentDateStr])) {
                $calendarData[$currentDateStr] = 'feriado';
            } elseif (isset($attendances[$currentDateStr])) {
                $calendarData[$currentDateStr] = 'asistencia';
            } elseif ($dayOfWeek !== 0 && $dayOfWeek !== 6) { 
                // Si no es feriado, no hay asistencia y NO es fin de semana, es inasistencia.
                $calendarData[$currentDateStr] = 'inasistencia';
            }
            // Los fines de semana sin registro simplemente se omiten (no son 'inasistencia')
        }

        return response()->json($calendarData);
    }
}
