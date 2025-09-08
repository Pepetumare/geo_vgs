<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestController extends Controller
{
    /**
     * Muestra el formulario para que el empleado cree una solicitud.
     */
    public function create()
    {
        return view('overtime.create');
    }

    /**
     * Guarda la nueva solicitud de horas extras en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.01|max:24',
            'description' => 'nullable|string|max:255',
        ]);

        Overtime::create([
            'user_id' => Auth::id(), // Asigna automáticamente el ID del usuario logueado
            'date' => $request->date,
            'hours' => $request->hours,
            'description' => $request->description,
            'status' => 'pending', // El estado por defecto es pendiente
        ]);

        return redirect()->route('dashboard')->with('status', 'Solicitud de horas extras enviada correctamente.');
    }
}
