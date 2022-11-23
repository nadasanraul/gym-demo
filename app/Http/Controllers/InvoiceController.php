<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data = array_merge(
                $data,
                [
                    'status' => InvoiceStatus::Outstanding,
                ],
            );

            $invoice = Invoice::query()->create($data);

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    public function update(Invoice $invoice, UpdateInvoiceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            Invoice::query()->update($data);

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    public function destroy(Invoice $invoice): JsonResponse|Response
    {
        try {
            $invoice->delete();

            return response()->noContent();
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
