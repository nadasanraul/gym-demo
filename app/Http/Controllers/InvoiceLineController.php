<?php

namespace App\Http\Controllers;

use App\Services\InvoicingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InvoiceLineController extends Controller
{
    /**
     * Endpoint to store an invoice line
     *
     * @param int $invoiceId
     * @param Request $request
     * @param InvoicingService $invoicingService
     * @return JsonResponse
     */
    public function store(int $invoiceId, Request $request, InvoicingService $invoicingService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|integer|min:0',
                'description' => 'required|string|max:255',
            ]);

            $validated = $validator->validated();

            $line = $invoicingService->addInvoiceLine($invoiceId, $validated);

            return response()->json($line);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
