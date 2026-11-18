<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Placeholder untuk menerima callback / webhook dari Payment Gateway (misal: Midtrans, Xendit, Tripay).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('Payment webhook received:', $request->all());

        $invoiceCode = $request->input('invoice_code') ?? $request->input('order_id');
        $paymentStatus = $request->input('transaction_status') ?? $request->input('status');

        if (! $invoiceCode) {
            return response()->json(['message' => 'Invoice code missing'], 400);
        }

        $invoice = Invoice::where('invoice_code', $invoiceCode)->first();

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        if (in_array($paymentStatus, ['settlement', 'capture', 'paid', 'success'], true)) {
            $invoice->update([
                'status' => InvoiceStatus::Paid,
                'paid_at' => now(),
                'payment_method' => $request->input('payment_type', 'Payment Gateway'),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook processed successfully',
        ]);
    }
}
