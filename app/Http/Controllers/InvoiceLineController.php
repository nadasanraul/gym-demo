<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceLineRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Throwable;

class InvoiceLineController extends Controller
{
    public function store(StoreInvoiceLineRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            /** @var Invoice $invoice */
            $invoice = Invoice::query()->find($validated['invoice_id']);

            $line = $invoice->lines()->create($validated);
            $invoice->update([
                'amount' => $invoice->amount + $line->amount,
            ]);

            return response()->json($line);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
