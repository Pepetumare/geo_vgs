<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
