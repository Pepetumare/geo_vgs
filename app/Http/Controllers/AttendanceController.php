<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
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
        $isSuspicious = false; // Flag por defecto

        // --- INICIO: LÓGICA ANTI-SPOOFING Y GEOCERCA ---
        if ($request->type == 'entrada') {
            // 1. Verificación de Geocerca (GPS vs Oficina)
            $companyLatitude = Config::get('company.location.latitude');
            $companyLongitude = Config::get('company.location.longitude');
            $allowedRadius = Config::get('company.radius_meters');

            $distanceFromCompany = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $companyLatitude,
                $companyLongitude
            );

            if ($distanceFromCompany > $allowedRadius) {
                return redirect()->route('dashboard')->with('error', 'Estás demasiado lejos para registrar tu ENTRADA.');
            }

            // 2. Verificación Anti-Spoofing (GPS vs IP)
            $ip = $request->ip();
            // Para pruebas en local, puedes simular una IP chilena: $ip = '190.114.255.255';
            $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,lat,lon");

            if ($response->successful() && $response->json('status') === 'success') {
                $ipLat = $response->json('lat');
                $ipLon = $response->json('lon');

                $distanceBetweenGpsAndIp = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $ipLat,
                    $ipLon
                );

                // Si la distancia entre el GPS y la IP es mayor a 100km, es sospechoso.
                if ($distanceBetweenGpsAndIp > 100000) { // 100 km en metros
                    $isSuspicious = true;
                }
            }
        }
        // --- FIN: LÓGICA ANTI-SPOOFING Y GEOCERCA ---

        // ... (La lógica de auto-corrección no cambia) ...
        if ($request->type == 'entrada') {
            if ($lastAttendance && $lastAttendance->type == 'entrada' && !$lastAttendance->created_at->isToday()) {
                $forgottenCheckoutTime = $lastAttendance->created_at->copy()->setTime(18, 0, 0);
                $user->attendances()->create([
                    'type' => 'salida',
                    'latitude' => $lastAttendance->latitude,
                    'longitude' => $lastAttendance->longitude,
                    'created_at' => $forgottenCheckoutTime,
                    'updated_at' => $forgottenCheckoutTime,
                ]);
                $successMessage = 'Turno anterior cerrado. ¡Nueva entrada registrada!';
            }
        }

        // ... (La lógica de ajuste de hora no cambia) ...
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
