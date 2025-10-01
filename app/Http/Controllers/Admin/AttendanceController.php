<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Formulario para crear un nuevo registro manual (1 solo).
     */
    public function createSingle()
    {
        $users = User::orderBy('name')->get();
        $companyLocation = [
            'lat' => Config::get('company.location.latitude'),
            'lng' => Config::get('company.location.longitude'),
        ];

        return view('admin.attendances.create-single', compact('users', 'companyLocation'));
    }

    /**
     * Guarda un nuevo registro manual (1 solo).
     */
    public function storeSingle(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'type'      => 'required|in:entrada,salida',
            'timestamp' => 'required|date',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $timestamp = Carbon::parse($request->timestamp);

        Attendance::create([
            'user_id'      => $request->user_id,
            'type'         => $request->type,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'created_at'   => $timestamp,
            'updated_at'   => $timestamp,
            'is_suspicious' => false,
            'ip_address'   => 'manual',
        ]);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Registro manual añadido con éxito.');
    }

    /**
     * Formulario para crear múltiples registros (N días).
     */
    public function createMultiple()
    {
        $users = User::orderBy('name')->get();
        $companyLocation = [
            'lat' => Config::get('company.location.latitude'),
            'lng' => Config::get('company.location.longitude'),
        ];

        return view('admin.attendances.create-multiple', compact('users', 'companyLocation'));
    }

    /**
     * Guarda múltiples registros (N días).
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'days'              => ['required', 'array', 'min:1'],
            'days.*.date'       => ['required', 'date'],
            'days.*.entry_time' => ['required', 'date_format:H:i'],
            'days.*.exit_time'  => ['required', 'date_format:H:i'],
        ]);

        $latitude  = config('company.location.latitude');
        $longitude = config('company.location.longitude');

        try {
            DB::transaction(function () use ($request, $latitude, $longitude) {
                foreach ($request->input('days', []) as $day) {
                    // Validación de negocio: salida > entrada (por día)
                    $entryTimestamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$day['date']} {$day['entry_time']}");
                    $exitTimestamp  = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$day['date']} {$day['exit_time']}");

                    if ($exitTimestamp->lessThanOrEqualTo($entryTimestamp)) {
                        throw new \RuntimeException("La salida debe ser posterior a la entrada para el día {$day['date']}.");
                    }

                    // Entrada
                    \App\Models\Attendance::create([
                        'user_id'      => $request->user_id,
                        'type'         => 'entrada',
                        'latitude'     => $latitude,
                        'longitude'    => $longitude,
                        'created_at'   => $entryTimestamp,
                        'updated_at'   => $entryTimestamp,
                        'is_suspicious' => false,
                        'ip_address'   => 'manual',
                    ]);

                    // Salida
                    \App\Models\Attendance::create([
                        'user_id'      => $request->user_id,
                        'type'         => 'salida',
                        'latitude'     => $latitude,
                        'longitude'    => $longitude,
                        'created_at'   => $exitTimestamp,
                        'updated_at'   => $exitTimestamp,
                        'is_suspicious' => false,
                        'ip_address'   => 'manual',
                    ]);
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->withErrors(['general' => 'No se pudieron guardar todos los registros: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.dashboard')
            ->with('status', 'Registros múltiples añadidos con éxito.');
    }



    /**
     * Formulario para editar un registro único.
     */
    public function editSingle(Attendance $attendance)
    {
        return view('admin.attendances.edit-single', compact('attendance'));
    }

    /**
     * Actualiza un registro único.
     */
    public function updateSingle(Request $request, Attendance $attendance)
    {
        $request->validate(['timestamp' => 'required|date']);
        $newTimestamp = Carbon::parse($request->timestamp);

        $attendance->created_at = $newTimestamp;
        $attendance->updated_at = now();
        $attendance->save();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Registro actualizado con éxito.');
    }

    /**
     * Formulario para editar un turno (entrada y salida).
     */
    public function edit(Attendance $entry, Attendance $exit)
    {
        return view('admin.attendances.edit', compact('entry', 'exit'));
    }

    /**
     * Actualiza los registros de un turno.
     */
    public function update(Request $request, Attendance $entry, Attendance $exit)
    {
        $request->validate([
            'entry_time' => 'required|date',
            'exit_time'  => 'required|date|after:entry_time',
        ]);

        DB::transaction(function () use ($request, $entry, $exit) {
            $newEntryTime = Carbon::parse($request->entry_time);
            $newExitTime  = Carbon::parse($request->exit_time);

            $entry->created_at = $newEntryTime;
            $entry->updated_at = now();
            $entry->save();

            $exit->created_at = $newExitTime;
            $exit->updated_at = now();
            $exit->save();
        });

        return redirect()->route('admin.reports')
            ->with('status', 'Turno actualizado con éxito.');
    }

    /**
     * Elimina un registro único.
     */
    public function destroySingle(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Registro eliminado exitosamente.');
    }
}
