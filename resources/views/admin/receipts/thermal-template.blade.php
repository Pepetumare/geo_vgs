<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Boleta N° {{ $receipt->correlative_number }}</title>
    <style>
        /* --- Estilos Finales para Impresora Térmica de 58mm --- */
        * {
            margin: 0;
            padding: 0;
            font-family: 'monospace';
            /* Fuente monoespaciada para alineación perfecta */
            font-size: 10pt;
            color: #000;
            box-sizing: border-box;
        }

        body {
            width: 58mm;
        }

        .container {
            padding: 2mm;
        }

        .header,
        .footer {
            text-align: center;
            margin-right: 20%;
        }

        .logo {
            max-width: 40mm;
            /* Tamaño del logo ajustado */
            height: auto;
            margin: 0 auto 5px auto;
            display: block;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .details p,
        .description-detail {
            margin-bottom: 3px;
            word-wrap: break-word;
        }

        .details strong {
            font-weight: bold;
        }

        .description-detail {
            white-space: pre-wrap;
        }

        /* --- ESTRUCTURA VERTICAL PARA EL RESUMEN --- */
        .summary-item {
            margin-bottom: 5px;
        }

        .summary-item .label {
            font-weight: bold;
            display: block;
            /* Ocupa toda la línea */
        }

        .summary-item .amount {
            display: block;
            /* Ocupa toda la línea */
            text-align: center;
            /* Alinea el monto a la derecha */
        }

        .summary-item .total-amount {
            font-weight: bold;
            font-size: 12pt;
        }

        .print-button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background: #333;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 12pt;
        }

        @media print {
            @page {
                margin: 0;
                size: 58mm auto;
            }

            html,
            body {
                width: 58mm;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none;
            }

            .container {
                padding: 1mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo-proserma.jpg') }}" alt="Logo" class="logo">
            <p>Boleta de Venta</p>
        </div>
        <div class="line"></div>
        <div class="details">
            <p><strong>Boleta N°:</strong> {{ str_pad($receipt->correlative_number, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Fecha:</strong> {{ $receipt->created_at->format('d/m/Y H:i') }}</p>
            <div class="line"></div>
            <p><strong>Cliente:</strong> {{ $receipt->client_name }}</p>
            @if ($receipt->client_rut)
                <p><strong>RUT:</strong> {{ $receipt->client_rut }}</p>
            @endif
        </div>
        <div class="line"></div>
        <p><strong>Detalle:</strong></p>
        <p class="description-detail">{{ $receipt->description }}</p>
        <div class="line"></div>
        <div class="summary">
            <div class="summary-item">
                <span class="label">Neto:</span>
                <span class="amount">$ {{ number_format($receipt->net_amount, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">IVA (19%):</span>
                <span class="amount">$ {{ number_format($receipt->iva_amount, 0, ',', '.') }}</span>
            </div>
            <div class="line"></div>
            <div class="summary-item">
                <span class="label">TOTAL:</span>
                <span class="amount total-amount">$ {{ number_format($receipt->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="line"></div>
        <div class="footer">
            <p>¡Gracias por su compra!</p>
        </div>
    </div>

    <button onclick="window.print();" class="no-print print-button">RE-IMPRIMIR</button>
</body>

</html>
