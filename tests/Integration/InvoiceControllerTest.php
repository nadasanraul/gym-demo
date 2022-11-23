<?php

namespace Tests\Integration;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\User;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    private User $user;

    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->invoice = Invoice::factory()->create(['user_id' => $this->user->id]);
        InvoiceLine::factory(10)->create(['invoice_id' => $this->invoice->id]);
    }

    public function testInvoiceListCanBeFetched()
    {
        $response = $this->get('api/invoices');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'description',
                'amount',
                'date',
                'lines' => [
                    '*' => [
                        'id',
                        'description',
                        'amount',
                    ],
                ],
                'user' => [
                    'id',
                    'email',
                    'name',
                ]
            ],
        ]);
    }

    public function testASingleInvoiceCanBeFetched()
    {
        $response = $this->get("api/invoices/{$this->invoice->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $this->invoice->id,
            'description' => $this->invoice->description,
            'date' => $this->invoice->date->format('Y-m-d H:i:s'),
            'amount' => $this->invoice->amount,
            'lines' => $this->invoice->lines->toArray(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
        ]);
    }

    public function testInvoiceCanBeCreated()
    {
        $response = $this->post('api/invoices', [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
        ]);

        $content = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $response->assertJson([
            'description' => 'Test invoice',
        ]);
        $this->assertDatabaseHas('invoices', [
            'user_id' => $this->user->id,
            'description' => $content['description'],
            'amount' => $content['amount'],
            'id' => $content['id']
        ]);
    }

    public function testInvoiceCannotBeCreatedWithProhibitedAttributes()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('api/invoices', [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
            'amount' => 1000,
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('invoices', [
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
            'amount' => 1000,
        ]);
    }

    public function testInvoiceCanBeUpdated()
    {
        $response = $this->patch("api/invoices/{$this->invoice->id}", [
            'description' => 'Updated description',
        ]);

        $content = json_decode($response->getContent(), true);
        $response->assertStatus(200);

        $response->assertJson([
            'id' => $this->invoice->id,
            'description' => 'Updated description',
        ]);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'description' => $content['description'],
        ]);
    }

    public function testInvoiceCannotBeUpdatedWithProhibitedAttributes()
    {
        $this->withoutExceptionHandling();

        $response = $this->patch("api/invoices/{$this->invoice->id}", [
            'description' => 'Updated description',
            'amount' => 1000,
            'date' => now(),
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'amount' => $this->invoice->amount,
            'date' => $this->invoice->date,
        ]);
    }

    public function testInvoiceCanBeDeleted()
    {
        $invoice = Invoice::factory()->create();
        $response = $this->delete("api/invoices/{$invoice->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function testInvoicesWithLinesCannotBeDeleted()
    {
        $this->withoutExceptionHandling();

        $response = $this->delete("api/invoices/{$this->invoice->id}");
        $response->assertStatus(400);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
        ]);
    }
}
