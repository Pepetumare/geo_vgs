<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config; // <-- Importar la clase Config

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('id', 'desc')->get();
        $lastAttendance = $user->attendances()->orderBy('id', 'desc')->first();
        $nextAction = (!$lastAttendance || $lastAttendance->type == 'salida') ? 'entrada' : 'salida';

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

        // --- INICIO DE LA LÓGICA DE GEOCERCA ---

        // Obtenemos la ubicación y radio desde el archivo de configuración
        $companyLatitude = Config::get('company.location.latitude');
        $companyLongitude = Config::get('company.location.longitude');
        $allowedRadius = Config::get('company.radius_meters');

        // Calculamos la distancia entre el empleado y la oficina
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $companyLatitude,
            $companyLongitude
        );

        // Validamos si la distancia es mayor a la permitida
        if ($distance > $allowedRadius) {
            return redirect()->route('dashboard')->with('error', 'Estás demasiado lejos de la ubicación permitida para realizar el marcaje.');
        }

        // --- FIN DE LA LÓGICA DE GEOCERCA ---

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

    /**
     * Calcula la distancia entre dos puntos geográficos usando la fórmula de Haversine.
     * Devuelve la distancia en metros.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radio de la Tierra en metros

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
