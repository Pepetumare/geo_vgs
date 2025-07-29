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
        // Obtenemos el último número correlativo para mostrarlo como referencia
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
            'net_amount' => 'required|integer|min:0',
        ]);

        $netAmount = $request->net_amount;
        $ivaAmount = round($netAmount * 0.19); // IVA del 19% en Chile
        $totalAmount = $netAmount + $ivaAmount;

        // Generar el número correlativo de forma segura
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

        // Redirigir a la vista de impresión con el recibo recién creado
        return redirect()->route('admin.receipts.show', $receipt);
    }

    /**
     * Muestra la boleta formateada para la impresora térmica.
     */
    public function show(Receipt $receipt)
    {
        return view('admin.receipts.thermal-template', ['receipt' => $receipt]);
    }
}