<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Muestra el dashboard con los registros y el estado actual del usuario.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtenemos todos los registros para el historial
        $attendances = $user->attendances()->orderBy('id', 'desc')->get();

        // Obtenemos el último registro por ID para determinar el estado actual (más fiable)
        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        
        // Determinamos cuál debería ser el próximo tipo de marcaje
        // Si no hay registros o el último fue 'salida', el próximo es 'entrada'.
        $nextAction = (!$lastAttendance || $lastAttendance->type == 'salida') ? 'entrada' : 'salida';

        return view('dashboard', [
            'attendances' => $attendances,
            'nextAction' => $nextAction,
        ]);
    }

    /**
     * Guarda un nuevo registro de asistencia.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:entrada,salida',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Lógica de validación para evitar marcajes dobles, usando el ID para más fiabilidad
        $lastAttendance = Auth::user()->attendances()->orderBy('id', 'desc')->first();
        if ($lastAttendance && $lastAttendance->type == $request->type) {
            return redirect()->route('dashboard')->with('error', 'Ya has realizado un marcaje de ' . $request->type . '.');
        }

        Auth::user()->attendances()->create([
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('dashboard')->with('status', '¡Registro de ' . $request->type . ' guardado con éxito!');
    }
}
