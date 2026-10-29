<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReceiptService
{
    /**
     * Generate PDF stream/download for an order receipt.
     */
    public function generatePdf(Order $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['items', 'seller', 'user']);

        return Pdf::loadView('receipts.thermal', [
            'order' => $order,
        ])->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm thermal receipt dimensions
    }
}
