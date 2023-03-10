<?php

namespace Tests\Integration;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\User;
use Tests\TestCase;

class InvoiceLineControllerTest extends TestCase
{
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->invoice = Invoice::factory()->create(['user_id' => $user->id]);
        InvoiceLine::factory(10)->create(['invoice_id' => $this->invoice->id]);
    }

    public function testInvoiceLineCanBeAddedToAnInvoice()
    {
        $response = $this->post("api/invoices/{$this->invoice->id}/lines", [
            'amount' => 1000,
            'description' => 'Test description',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'amount' => 1000,
            'description' => 'Test description',
        ]);
    }

    public function testInvoiceLineCannotBeCreatedWithNegativeAmount()
    {
        $response = $this->post("api/invoices/{$this->invoice->id}/lines", [
            'amount' => -1000,
            'description' => 'Test description',
        ]);

        $response->assertStatus(400);
    }
}
