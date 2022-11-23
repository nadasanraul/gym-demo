<?php

namespace App\Http\Controllers;

use App\Services\InvoicingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InvoiceLineController extends Controller
{
    public function store(int $invoiceId, Request $request, InvoicingService $invoicingService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|integer|min:0',
                'description' => 'required|string|max:255',
            ]);

            $validated = $validator->validated();

            $line = $invoicingService->createInvoiceLine($invoiceId, $validated);

            return response()->json($line);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
