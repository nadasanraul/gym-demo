<?php

namespace App\Http\Controllers;

use App\Services\InvoicingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct(private InvoicingService $invoicingService)
    {
    }

    /**
     * Endpoint to get all invoices
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $invoices = $this->invoicingService->getInvoices();

            return response()->json([
                ...$invoices,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    /**
     * Endpoint to get a single invoice based on its ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoicingService->getInvoice($id);

            return response()->json([
                $invoice,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    /**
     * Endpoint to store an invoice
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:users,id',
                'amount' => 'prohibited',
                'status' => 'prohibited',
                'date' => 'prohibited',
            ]);

            $data = $validator->validated();

            $invoice = $this->invoicingService->createInvoice($data);

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    /**
     * Endpoint to update an invoice based on the id
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:Outstanding,Paid,Void',
                'amount' => 'prohibited',
                'date' => 'prohibited',
            ]);

            $data = $validator->validated();

            $invoice = $this->invoicingService->updateInvoice($id, $data);

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    /**
     * Endpoint to delete an invoice based on the id
     *
     * @param int $id
     * @return JsonResponse|Response
     */
    public function destroy(int $id): JsonResponse|Response
    {
        try {
            $this->invoicingService->deleteInvoice($id);

            return response()->noContent();
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
