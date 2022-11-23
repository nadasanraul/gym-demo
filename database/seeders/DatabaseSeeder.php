<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::factory(10)->create();

        foreach (User::all() as $user) {
            Membership::factory()->create(['user_id' => $user->id]);
            $invoice = Invoice::factory()->create(['user_id' => $user->id]);
            InvoiceLine::factory(10)->create(['invoice_id' => $invoice->id]);
         }
    }
}
