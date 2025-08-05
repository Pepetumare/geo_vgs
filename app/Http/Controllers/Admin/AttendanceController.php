<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB; // <-- Importante para transacciones
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo registro de asistencia manual.
     */
    public function createSingle()
    {
        $users = User::orderBy('name')->get();
        $companyLocation = [
            'lat' => Config::get('company.location.latitude'),
            'lng' => Config::get('company.location.longitude'),
        ];

        return view('admin.attendances.create-single', [
            'users' => $users,
            'companyLocation' => $companyLocation,
        ]);
    }

    /**
     * Guarda un nuevo registro de asistencia manual en la base de datos.
     */
    public function storeSingle(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:entrada,salida',
            'timestamp' => 'required|date',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $timestamp = Carbon::parse($request->timestamp);

        $attendance = new Attendance();
        $attendance->user_id = $request->user_id;
        $attendance->type = $request->type;
        $attendance->latitude = $request->latitude;
        $attendance->longitude = $request->longitude;
        $attendance->created_at = $timestamp;
        $attendance->updated_at = $timestamp;
        $attendance->is_suspicious = false;
        $attendance->ip_address = 'manual';
        $attendance->save();

        return redirect()->route('admin.dashboard')->with('status', 'Registro manual añadido con éxito.');
    }

    /**
     * Muestra el formulario para editar un único registro de asistencia.
     */
    public function editSingle(Attendance $attendance)
    {
        return view('admin.attendances.edit-single', ['attendance' => $attendance]);
    }

    /**
     * Actualiza un único registro de asistencia.
     */
    public function updateSingle(Request $request, Attendance $attendance)
    {
        $request->validate(['timestamp' => 'required|date']);
        $newTimestamp = Carbon::parse($request->timestamp);

        $attendance->created_at = $newTimestamp;
        $attendance->save();

        return redirect()->route('admin.dashboard')->with('status', 'Registro actualizado con éxito.');
    }

    /**
     * Muestra el formulario para editar un turno completo (entrada y salida).
     */
    public function edit(Attendance $entry, Attendance $exit)
    {
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

        // --- CORRECCIÓN CLAVE Y MEJORA DE ROBUSTEZ ---
        DB::transaction(function () use ($request, $entry, $exit) {
            $newEntryTime = Carbon::parse($request->entry_time);
            $newExitTime = Carbon::parse($request->exit_time);

            // Usamos el método explícito para asegurar que los cambios se guarden
            $entry->created_at = $newEntryTime;
            $entry->updated_at = now();
            $entry->save();

            $exit->created_at = $newExitTime;
            $exit->updated_at = now();
            $exit->save();
        });

        return redirect()->route('admin.reports')->with('status', 'Turno actualizado con éxito.');
    }
}
