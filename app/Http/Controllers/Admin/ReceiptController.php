<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo recibo.
     */
    public function create()
    {
        $lastCorrelative = Receipt::max('correlative_number') ?? 0;
        $nextCorrelative = $lastCorrelative + 1;

        return view('admin.receipts.create', ['nextCorrelative' => $nextCorrelative]);
    }

    /**
     * Guarda el nuevo recibo y redirige a la vista de impresión.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_rut' => 'nullable|string|max:20',
            'description' => 'required|string',
            'total_amount' => 'required|integer|min:0', // Ahora validamos el monto total
        ]);

        // --- INICIO: LÓGICA DE CÁLCULO DE IVA INVERSO ---
        $totalAmount = $request->total_amount;
        $netAmount = round($totalAmount / 1.19);
        $ivaAmount = $totalAmount - $netAmount;
        // --- FIN: LÓGICA DE CÁLCULO ---

        $lastCorrelative = Receipt::max('correlative_number') ?? 0;
        $newCorrelative = $lastCorrelative + 1;

        $receipt = Receipt::create([
            'correlative_number' => $newCorrelative,
            'client_name' => $request->client_name,
            'client_rut' => $request->client_rut,
            'description' => $request->description,
            'net_amount' => $netAmount,
            'iva_amount' => $ivaAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('admin.receipts.show', $receipt);
    }

    /**
     * Muestra la boleta formateada para la impresora térmica.
     */
    public function show(Receipt $receipt)
    {
        return view('admin.receipts.thermal-template', ['receipt' => $receipt]);
    }

    /**
     * Muestra el historial de boletas y permite buscar por RUT.
     */
    public function history(Request $request)
    {
        $receipts = collect();
        $searchRut = $request->input('client_rut');

        if ($searchRut) {
            $receipts = Receipt::where('client_rut', $searchRut)
                ->orderBy('correlative_number', 'desc')
                ->get();
        }

        return view('admin.receipts.history', [
            'receipts' => $receipts,
            'searchRut' => $searchRut,
        ]);
    }
}
