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
            // Verificación de Geocerca y Anti-Spoofing
            $companyLatitude = Config::get('company.location.latitude');
            $companyLongitude = Config::get('company.location.longitude');
            $allowedRadius = Config::get('company.radius_meters');
            $distanceFromCompany = $this->calculateDistance($request->latitude, $request->longitude, $companyLatitude, $companyLongitude);
            if ($distanceFromCompany > $allowedRadius) {
                return redirect()->route('dashboard')->with('error', 'Estás demasiado lejos para registrar tu ENTRADA.');
            }
            $ip = $request->ip();
            $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,lat,lon");
            if ($response->successful() && $response->json('status') === 'success') {
                $ipLat = $response->json('lat');
                $ipLon = $response->json('lon');
                $distanceBetweenGpsAndIp = $this->calculateDistance($request->latitude, $request->longitude, $ipLat, $ipLon);
                if ($distanceBetweenGpsAndIp > 100000) {
                    $isSuspicious = true;
                }
            }

            // Lógica de auto-corrección
            if ($lastAttendance && $lastAttendance->type == 'entrada' && !$lastAttendance->created_at->isToday()) {
                $forgottenCheckoutTime = $lastAttendance->created_at->copy()->setTime(18, 30, 0);
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

        $newAttendance = new Attendance();
        $newAttendance->user_id = $user->id;
        $newAttendance->type = $request->type;
        $newAttendance->latitude = $request->latitude;
        $newAttendance->longitude = $request->longitude;
        $newAttendance->ip_address = $request->ip();
        $newAttendance->is_suspicious = $isSuspicious;

        // Lógica de ajuste de hora para salidas tardías
        // NOTA PARA PRUEBAS: Cambia el 20 por la hora actual (ej. 11) para probar.
        if ($request->type == 'salida' && now()->hour >= 20) {
            $timestamp = now()->setHour(18)->setMinute(30)->setSecond(0);
            $newAttendance->created_at = $timestamp;
            $newAttendance->updated_at = $timestamp;
        }

        // Guardamos el nuevo registro. Este método es más explícito y fiable.
        $newAttendance->save();

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
