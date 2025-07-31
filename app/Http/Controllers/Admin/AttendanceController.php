<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Muestra el formulario para editar un turno (entrada y salida).
     */
    public function editSingle(Attendance $attendance)
    {
        return view('admin.attendances.edit-single', ['attendance' => $attendance]);
    }

    public function updateSingle(Request $request, Attendance $attendance)
    {
        $request->validate([
            'timestamp' => 'required|date',
        ]);

        $newTimestamp = Carbon::parse($request->timestamp);

        $attendance->created_at = $newTimestamp;
        
        $attendance->save();

        return redirect()->route('admin.dashboard')->with('status', 'Registro actualizado con éxito.');
    }

    public function edit(Attendance $entry, Attendance $exit)
    {
        // Pasamos los dos registros (entrada y salida) a la vista de edición.
        return view('admin.attendances.edit', [
            'entry' => $entry,
            'exit' => $exit,
        ]);
    }

    /**
     * Actualiza los registros de un turno en la base de datos.
     */
    public function update(Request $request, Attendance $entry, Attendance $exit)
    {
        $request->validate([
            'entry_time' => 'required|date',
            'exit_time' => 'required|date|after:entry_time',
        ]);

        // Convertimos las fechas del formulario a objetos Carbon.
        $newEntryTime = Carbon::parse($request->entry_time);
        $newExitTime = Carbon::parse($request->exit_time);

        // Actualizamos el registro de entrada.
        $entry->update([
            'created_at' => $newEntryTime,
            'updated_at' => now(), // Marcar que fue actualizado ahora
        ]);

        // Actualizamos el registro de salida.
        $exit->update([
            'created_at' => $newExitTime,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reports')->with('status', 'Turno actualizado con éxito.');
    }
}
