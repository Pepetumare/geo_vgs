<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Auth::user()->attendances()->orderBy('created_at', 'desc')->get();
        return view('dashboard', ['attendances' => $attendances]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:entrada,salida',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Auth::user()->attendances()->create([
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('dashboard')->with('status', '¡Registro guardado con éxito!');
    }
}
