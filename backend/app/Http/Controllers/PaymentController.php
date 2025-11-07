<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request, $saleId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank,wallet,cod',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sale = Sale::findOrFail($saleId);

        // Validate amount doesn't exceed balance
        if ($request->amount > $sale->balance_due) {
            return response()->json([
                'error' => 'Payment amount exceeds balance due'
            ], 422);
        }

        try {
            $payment = $sale->recordPayment(
                $request->amount,
                $request->method,
                $request->reference,
                $request->notes
            );

            $sale->load('payments');

            return response()->json([
                'message' => 'Payment recorded successfully',
                'payment' => $payment,
                'sale' => $sale
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment recording failed', [
                'sale_id' => $saleId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to record payment',
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function index($saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            $payments = $sale->payments()
                ->orderBy('paid_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'sale_id' => $payment->sale_id,
                        'method' => $payment->method,
                        'amount' => $payment->amount,
                        'reference' => $payment->reference,
                        'paid_at' => $payment->paid_at,
                        'notes' => $payment->notes,
                        'created_by' => $payment->created_by,
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at
                    ];
                });

            return response()->json($payments);
        } catch (\Exception $e) {
            \Log::error('Failed to load payments', [
                'sale_id' => $saleId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to load payments',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
