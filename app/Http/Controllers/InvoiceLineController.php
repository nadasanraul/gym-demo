<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InvoiceLineController extends Controller
{
    public function store(Invoice $invoice, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|integer|min:0',
                'description' => 'required|string|max:255',
            ]);

            $validated = $validator->validated();

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
