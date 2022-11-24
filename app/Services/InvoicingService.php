<?php

namespace App\Services;

use App\Models\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InvoicingService
{
    /**
     * Retrieving all the invoices from the database with the lines and the user
     *
     * @return Collection
     */
    public function getInvoices(): Collection
    {
        $invoices = Invoice::with(['lines', 'user'])->get();

        return $invoices;
    }

    /**
     * Retrieving a single invoice from the database
     *
     * @param int $id
     */
    public function getInvoice(int $id)
    {
        $invoice = Invoice::with(['lines', 'user'])->findOrFail($id);

        return $invoice;
    }

    /**
     * Storing the invoice in the database
     *
     * @param array $attributes
     * @return Invoice
     */
    public function createInvoice(array $attributes): Invoice
    {
        if (isset($attributes['amount']) && $attributes['amount'] < 0) {
            throw new Exception('Invoice amount must be positive');
        }

        if (!isset($attributes['status'])) {
            $attributes['status'] = InvoiceStatus::Outstanding;
        }

        $attributes['date'] = now();

        $invoice = Invoice::create($attributes);
        $invoice->refresh();

        return $invoice;
    }

    /**
     * Updating an invoice based on the ID
     * Properties are refreshed before returning in order to be the most up to date
     *
     * @param int $id
     * @param array $attributes
     * @return Invoice
     */
    public function updateInvoice(int $id, array $attributes): Invoice
    {
        if (isset($attributes['amount']) && $attributes['amount'] < 0) {
            throw new Exception('Invoice amount must be positive');
        }

        $invoice = Invoice::findOrFail($id);

        $invoice->update($attributes);
        $invoice->refresh();

        return $invoice;
    }

    /**
     * Removing an invoice from the database
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function deleteInvoice(int $id): void
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->lines->isNotEmpty()) {
            throw new Exception('Only empty invoices are allowed to be deleted');
        }

        $invoice->delete();
    }

    /**
     * Storing an invoice line for an invoice in the database and updating the invoice amount
     *
     * @param int $invoiceId
     * @param array $attributes
     * @return InvoiceLine
     */
    public function addInvoiceLine(int $invoiceId, array $attributes): InvoiceLine
    {
        if (isset($attributes['amount']) && $attributes['amount'] < 0) {
            throw new Exception('Invoice amount must be positive');
        }

        $attributes['invoice_id'] = $invoiceId;

        $line = InvoiceLine::create($attributes);
        Invoice::where('id', $invoiceId)->increment('amount', $line->amount);

        return $line;
    }

    /**
     * Getting the invoice for the current month for a user.
     * If no invoice exists, it gets created
     *
     * @param User $user
     * @return Model
     */
    public function getCurrentInvoiceForUser(User $user): Model
    {
        return $user->invoices()
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
    }
}
