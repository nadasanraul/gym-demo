<?php

namespace App\Http\Controllers;

use App\Models\Enums\InvoiceStatus;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
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
            $data = array_merge(
                $data,
                [
                    'status' => InvoiceStatus::Outstanding,
                ],
            );

            $invoice = Invoice::query()->create($data);
            $invoice->refresh();

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    public function update(Invoice $invoice, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:Outstanding,Paid,Void',
                'amount' => 'prohibited',
                'date' => 'prohibited',
            ]);

            $data = $validator->validated();

            Invoice::query()->update($data);
            $invoice->refresh();

            return response()->json($invoice);
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }

    public function destroy(Invoice $invoice): JsonResponse|Response
    {
        try {
            if ($invoice->lines->isNotEmpty()) {
                throw new Exception('Only empty invoices are allowed to be deleted');
            }
            $invoice->delete();

            return response()->noContent();
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
