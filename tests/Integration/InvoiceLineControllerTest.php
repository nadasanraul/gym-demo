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

        $content = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $response->assertJson([
            'amount' => 1000,
            'description' => 'Test description',
        ]);
        $this->assertDatabaseHas('invoice_lines', [
            'id' => $content['id'],
            'amount' => 1000,
            'description' => 'Test description',
        ]);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount + 1000,
        ]);
    }

    public function testInvoiceLineCannotBeCreatedWithNegativeAmount()
    {
        $response = $this->post("api/invoices/{$this->invoice->id}/lines", [
            'amount' => -1000,
            'description' => 'Test description',
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('invoice_lines', [
            'amount' => -1000,
        ]);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount,
        ]);
    }
}
