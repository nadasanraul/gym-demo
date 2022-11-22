<?php

namespace App\Http\Controllers;

use App\Models\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class UserController extends Controller
{
    public function checkin(User $user): JsonResponse|Response
    {
        try {
            /** @var Membership $membership */
            $membership = $user->membership;

            if (!$membership) {
                throw new Exception('No membership found!');
            }

            if (!$membership->isActive()) {
                throw new Exception('Cannot check in with a non-active membership');
            }

            if ($membership->isNotStarted()) {
                throw new Exception('Your current membership is expired');
            }

            if ($membership->isExpired()) {
                throw new Exception('Your current membership is expired');
            }

            if ($membership->isEmpty()) {
                throw new Exception('You have no credits left on your membership!');
            }

            $membership->update([
                'credits' => $membership->credits - 1,
            ]);
            /** @var Invoice $invoice */
            $invoice = $user->invoices()
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->firstOrCreate(
                    [
                        'status' => InvoiceStatus::Outstanding,
                    ],
                    [
                        'description' => "Invoice for {$user->name}.}",
                        'date' => now(),
                    ]
                );

            $line = $invoice->lines()->create([
                'amount' => 1000,
                'description' => 'Invoice line for the checkin on the date of ' . now()->format('d-m-Y'),
            ]);

            $invoice->update([
                'amount' => $invoice->amount + $line->amount,
            ]);

            return response()->noContent();
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e, debug: true);
        }
    }
}
