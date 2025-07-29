<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Importar la librería PDF

class PayslipController extends Controller
{
    /**
     * Muestra la página para generar una nueva boleta.
     */
    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.payslips.create', ['users' => $users]);
    }

    /**
     * Genera y descarga la boleta de pago en PDF.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $user = User::findOrFail($request->user_id);
        $period = Carbon::parse($request->month);
        $startDate = $period->copy()->startOfMonth();
        $endDate = $period->copy()->endOfMonth();

        // Lógica para calcular las horas (similar a la de reportes)
        $attendances = $user->attendances()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalSeconds = 0;
        $currentEntry = null;
        foreach ($attendances as $record) {
            if ($record->type == 'entrada' && is_null($currentEntry)) {
                $currentEntry = $record;
            } elseif ($record->type == 'salida' && !is_null($currentEntry)) {
                $totalSeconds += $currentEntry->created_at->diffInSeconds($record->created_at);
                $currentEntry = null;
            }
        }
        $totalHours = $totalSeconds / 3600;

        // Aquí definirías el sueldo base y otros cálculos
        $sueldoBase = 500000; // Ejemplo
        $valorHora = 3500; // Ejemplo
        $sueldoTotal = $totalHours * $valorHora;

        $data = [
            'user' => $user,
            'period' => $period->format('F Y'),
            'total_hours' => $totalHours,
            'sueldo_base' => $sueldoBase,
            'sueldo_total' => $sueldoTotal,
        ];

        // Cargar la vista de la boleta con los datos y generar el PDF
        $pdf = Pdf::loadView('admin.payslips.template', $data);

        // Descargar el PDF
        return $pdf->download('boleta-'.$user->name.'-'.$period->format('m-Y').'.pdf');
    }
}
