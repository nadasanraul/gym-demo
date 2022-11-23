<?php

namespace Tests\Unit;

use App\Models\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\User;
use App\Services\InvoicingService;
use Exception;
use Tests\TestCase;

class InvoicingServiceTest extends TestCase
{
    private User $user;

    private Invoice $invoice;

    private InvoicingService $invoicingService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->invoicingService = app(InvoicingService::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->invoice = Invoice::factory()->create(['user_id' => $this->user->id]);
        InvoiceLine::factory(10)->create(['invoice_id' => $this->invoice->id]);
    }

    public function testInvoiceCanBeStored()
    {
        $attributes = [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
        ];
        $invoice = $this->invoicingService->createInvoice($attributes);

        $this->assertEquals('Test invoice', $invoice->description);
        $this->assertEquals($this->user->id, $invoice->user_id);
        $this->assertDatabaseHas('invoices', [
            'user_id' => $this->user->id,
            'description' => $invoice->description,
            'amount' => $invoice->amount,
            'status' => InvoiceStatus::Outstanding,
            'id' => $invoice->id,
        ]);
    }

    public function testInvoiceCannotBeCreatedWithANegativeAmount()
    {
        $this->withoutExceptionHandling();
        $attributes = [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
            'amount' => -1000,
        ];

        $this->expectException(Exception::class);

        $this->invoicingService->createInvoice($attributes);

        $this->assertDatabaseMissing('invoices', [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
            'amount' => -1000,
        ]);
    }

    public function testInvoiceCanBeUpdated()
    {
        $attributes =  [
            'description' => 'Updated description',
        ];

        $invoice = $this->invoicingService->updateInvoice($this->invoice->id, $attributes);

        $this->assertEquals('Updated description', $invoice->description);
        $this->assertDatabaseHas('invoices', [
            'description' => 'Updated description',
            'amount' => $invoice->amount,
            'status' => $invoice->status,
            'id' => $invoice->id,
        ]);
    }

    public function testInvoiceCannotBeUpdatedWithNegativeAmount()
    {
        $this->withoutExceptionHandling();
        $attributes = [
            'amount' => -1000,
        ];

        $this->expectException(Exception::class);

        $this->invoicingService->updateInvoice($this->invoice->id, $attributes);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount,
        ]);
    }

    public function testInvoiceCanBeDeleted()
    {
        $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);

        $this->invoicingService->deleteInvoice($invoice->id);

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function testInvoiceWithLinesCannotBeDeleted()
    {
        $this->withoutExceptionHandling();

        $this->expectException(Exception::class);

        $this->invoicingService->deleteInvoice($this->invoice->id);
    }

    public function testInvoiceLineCanBeAddedToInvoice()
    {
        $attributes = [
            'amount' => 1000,
            'description' => 'Test description',
        ];

        $line = $this->invoicingService->addInvoiceLine($this->invoice->id, $attributes);

        $this->assertDatabaseHas('invoice_lines', [
            'id' => $line->id,
            'amount' => 1000,
            'description' => 'Test description',
        ]);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount + 1000,
        ]);
    }

    public function testInvoiceLineCannotBeAddedWithANegativeAmount()
    {
        $this->withoutExceptionHandling();

        $attributes = [
            'amount' => -1000,
            'description' => 'Test description',
        ];

        $this->expectException(Exception::class);

        $this->invoicingService->addInvoiceLine($this->invoice->id, $attributes);

        $this->assertDatabaseMissing('invoice_lines', [
            'amount' => -1000,
        ]);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount,
        ]);
    }
}
