<?php

namespace App\Http\Controllers;

use App\Models\Attendance; // <-- Asegúrate de que el modelo Attendance esté importado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('id', 'desc')->get();
        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        
        $nextAction = 'entrada';

        if ($lastAttendance) {
            if ($lastAttendance->type == 'entrada' && $lastAttendance->created_at->isToday()) {
                $nextAction = 'salida';
            }
        }

        return view('dashboard', [
            'attendances' => $attendances,
            'nextAction' => $nextAction,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:entrada,salida',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        $successMessage = '¡Registro de ' . $request->type . ' guardado con éxito!';

        if ($request->type == 'entrada') {
            if ($lastAttendance && $lastAttendance->type == 'entrada' && !$lastAttendance->created_at->isToday()) {
                
                $forgottenCheckoutTime = $lastAttendance->created_at->copy()->setTime(18, 0, 0);

                $forgottenAttendance = new Attendance();
                $forgottenAttendance->user_id = $user->id;
                $forgottenAttendance->type = 'salida';
                $forgottenAttendance->latitude = $lastAttendance->latitude;
                $forgottenAttendance->longitude = $lastAttendance->longitude;
                $forgottenAttendance->created_at = $forgottenCheckoutTime;
                $forgottenAttendance->updated_at = $forgottenCheckoutTime;
                $forgottenAttendance->save();

                $successMessage = 'Turno anterior cerrado automáticamente. ¡Nueva entrada registrada con éxito!';
            }
        }

        if ($request->type == 'entrada') {
            $companyLatitude = Config::get('company.location.latitude');
            $companyLongitude = Config::get('company.location.longitude');
            $allowedRadius = Config::get('company.radius_meters');

            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $companyLatitude,
                $companyLongitude
            );

            if ($distance > $allowedRadius) {
                return redirect()->route('dashboard')->with('error', 'Estás demasiado lejos para registrar tu ENTRADA.');
            }
        }

        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        if ($lastAttendance && $lastAttendance->type == $request->type && $lastAttendance->created_at->isToday()) {
            return redirect()->route('dashboard')->with('error', 'Ya has realizado un marcaje de ' . $request->type . ' hoy.');
        }

        // Preparamos los datos para el registro actual.
        $attendanceData = [
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        // --- INICIO: LÓGICA DE AJUSTE DE HORA PARA SALIDAS TARDÍAS ---
        // Si el marcaje es de 'salida' y la hora actual es 8 PM (20:00) o más tarde...
        if ($request->type == 'salida' && now()->hour >= 20) {
            // ...modificamos la hora de creación para que sea a las 6 PM (18:00) del día de hoy.
            $timestamp = now()->setHour(18)->setMinute(0)->setSecond(0);
            
            $attendanceData['created_at'] = $timestamp;
            $attendanceData['updated_at'] = $timestamp;
        }
        // --- FIN: LÓGICA DE AJUSTE DE HORA ---

        $user->attendances()->create($attendanceData);

        return redirect()->route('dashboard')->with('status', $successMessage);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return $angle * $earthRadius;
    }
}
