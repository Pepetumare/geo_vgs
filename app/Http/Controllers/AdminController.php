<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Holiday;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = User::orderBy('name')->get();
        $query = User::query();

        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $query->where(function ($q) {
            $q->whereHas('attendances')->orWhereHas('overtimes');
        });

        $usersWithRecords = $query->with(['attendances' => function ($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->whereDate('created_at', '<=', $request->end_date);
            }
        }, 'overtimes' => function ($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->where('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->where('date', '<=', $request->end_date);
            }
        }])->get();

        foreach ($usersWithRecords as $user) {
            $attendances = $user->attendances->map(function ($item) {
                $item->record_date = $item->created_at;
                return $item;
            });
            $overtimes = $user->overtimes->map(function ($item) {
                $item->record_date = Carbon::parse($item->date);
                return $item;
            });

            $user->combinedRecords = $attendances->concat($overtimes)->sortByDesc('record_date');
        }

        return view('admin.dashboard', [
            'users' => $usersWithRecords,
            'allUsers' => $allUsers,
            'filters' => $request->only(['user_id', 'start_date', 'end_date']),
        ]);
    }

    public function getCalendarData(User $user, $year, $month)
    {
        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Mes inválido'], 400);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = $user->attendances()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()->keyBy(fn($item) => $item->created_at->format('Y-m-d'));

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->get()->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $calendarData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $currentDateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;

            if (isset($holidays[$currentDateStr])) {
                $calendarData[$currentDateStr] = 'feriado';
            } elseif (isset($attendances[$currentDateStr])) {
                $calendarData[$currentDateStr] = 'asistencia';
            } elseif ($dayOfWeek !== 0 && $dayOfWeek !== 6) {
                $calendarData[$currentDateStr] = 'inasistencia';
            }
        }

        return response()->json($calendarData);
    }

    // --- MÉTODOS PARA GESTIÓN DE HORAS EXTRAS POR PARTE DEL ADMIN ---

    /**
     * Muestra la página con el formulario para que el admin añada horas extras.
     */
    public function createOvertime()
    {
        $allUsers = User::orderBy('name')->get();
        return view('admin.overtime.create', compact('allUsers'));
    }

    /**
     * Guarda las horas extras añadidas por el admin (automáticamente aprobadas).
     */
    public function storeOvertime(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.01|max:24',
            'description' => 'nullable|string|max:255',
        ]);

        Overtime::create(array_merge($request->all(), [
            'status' => 'approved'
        ]));

        return redirect()->route('admin.dashboard')->with('status', 'Horas extras añadidas exitosamente.');
    }

    /**
     * Muestra la página para gestionar las solicitudes de horas extras.
     */
    public function overtimeRequests()
    {
        $pendingRequests = Overtime::with('user')->where('status', 'pending')->latest()->get();
        $processedRequests = Overtime::with('user')->whereIn('status', ['approved', 'rejected'])->latest()->take(50)->get();

        return view('admin.overtime.requests', compact('pendingRequests', 'processedRequests'));
    }

    /**
     * Actualiza el estado de una solicitud (aprueba o rechaza).
     */
    public function updateOvertimeRequest(Request $request, Overtime $overtime)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $overtime->update(['status' => $request->status]);

        return redirect()->route('admin.overtime.requests')->with('status', 'Solicitud actualizada.');
    }

    /**
     * Elimina un registro de horas extras.
     */
    public function destroyOvertime(Overtime $overtime)
    {
        $overtime->delete();
        return redirect()->back()->with('status', 'Registro de horas extras eliminado.');
    }
}
