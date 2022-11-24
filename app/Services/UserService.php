<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Membership;
use App\Models\User;
use Exception;

class UserService
{
    public function __construct(private InvoicingService $invoicingService)
    {
    }

    /**
     * Checking in a user in and updating the invoice
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function checkinUser(int $id): void
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        /** @var Membership $membership */
        $membership = $user->membership;

        if (!$membership) {
            throw new Exception('No membership found!');
        }

        if (!$membership->isActive()) {
            throw new Exception('Cannot check in with a cancelled membership!');
        }

        if ($membership->isNotStarted()) {
            throw new Exception('Your membership is not started yet!');
        }

        if ($membership->isExpired()) {
            throw new Exception('Your current membership is expired!');
        }

        if ($membership->isEmpty()) {
            throw new Exception('You have no credits left on your membership!');
        }

        $membership->update([
            'credits' => $membership->credits - 1,
        ]);
        /** @var Invoice $invoice */
        $invoice = $this->invoicingService->getCurrentInvoiceForUser($user);

        $invoiceLineAttributes = [
            'amount' => 1000,
            'description' => 'Invoice line for the checkin on the date of ' . now()->format('d-m-Y'),
        ];
        $this->invoicingService->addInvoiceLine($invoice->id, $invoiceLineAttributes);
    }
}
