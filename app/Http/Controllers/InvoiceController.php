<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Throwable;

class InvoiceController extends Controller
{
    /**
     * Endpoint to get all invoices
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                ...Invoice::query()->with(['lines', 'user'])->get(),
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    /**
     * Endpoint to get a single invoice based on its ID
     *
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function show(Invoice $invoice): JsonResponse
    {
        try {
            $invoice->load(['lines', 'user']);

            return response()->json([
                $invoice,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
