<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http; // <-- Importante: Añadir el cliente HTTP
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('id', 'desc')->paginate(4);
        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();

        $nextAction = 'entrada';
        $clockInTime = null;

        if ($lastAttendance) {
            if ($lastAttendance->type == 'entrada' && $lastAttendance->created_at->isToday()) {
                $nextAction = 'salida';
                $clockInTime = $lastAttendance->created_at;
            }
        }

        return view('dashboard', [
            'attendances' => $attendances,
            'nextAction' => $nextAction,
            'clockInTime' => $clockInTime,
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
        $isSuspicious = false;

        if ($request->type == 'entrada') {
            // 1. Verificación de Geocerca (GPS vs Oficina)
            $companyLatitude = Config::get('company.location.latitude');
            $companyLongitude = Config::get('company.location.longitude');
            $allowedRadius = Config::get('company.radius_meters');

            $distanceFromCompany = $this->calculateDistance($request->latitude, $request->longitude, $companyLatitude, $companyLongitude);

            if ($distanceFromCompany > $allowedRadius) {
                return redirect()->route('dashboard')->with('error', 'Estás demasiado lejos para registrar tu ENTRADA.');
            }

            // 2. Verificación Anti-Spoofing (GPS vs IP)
            $ip = $request->ip();
            // Para pruebas en local: $ip = '190.114.255.255'; // IP de ejemplo de Chile
            $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,lat,lon");

            if ($response->successful() && $response->json('status') === 'success') {
                $ipLat = $response->json('lat');
                $ipLon = $response->json('lon');
                $distanceBetweenGpsAndIp = $this->calculateDistance($request->latitude, $request->longitude, $ipLat, $ipLon);

                if ($distanceBetweenGpsAndIp > 100000) { // 100 km en metros
                    $isSuspicious = true;
                }
            }

            // Lógica de auto-corrección
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

        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        if ($lastAttendance && $lastAttendance->type == $request->type && $lastAttendance->created_at->isToday()) {
            return redirect()->route('dashboard')->with('error', 'Ya has realizado un marcaje de ' . $request->type . ' hoy.');
        }

        $attendanceData = [
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'ip_address' => $request->ip(),
            'is_suspicious' => $isSuspicious,
        ];
        if ($request->type == 'salida' && now()->hour >= 20) {
            $timestamp = now()->setHour(18)->setMinute(0)->setSecond(0);
            $attendanceData['created_at'] = $timestamp;
            $attendanceData['updated_at'] = $timestamp;
        }

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
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
