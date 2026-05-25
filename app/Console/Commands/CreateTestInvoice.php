<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test invoice for testing notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        if (!$user) {
            $this->error('No user found. Please create a user first.');
            return;
        }

        $client = Client::first();
        if (!$client) {
            $client = Client::create([
                'user_id' => $user->id,
                'name' => 'Test Client',
                'email' => 'test@example.com',
                'phone' => '1234567890'
            ]);
            $this->info('Created test client: ' . $client->name);
        }

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'invoice_number' => 'TEST-' . date('YmdHis'),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 500.00,
            'balance_due' => 500.00,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'currency' => 'UGX'
        ]);

        $this->info('Test invoice created successfully!');
        $this->info('Invoice ID: ' . $invoice->id);
        $this->info('Invoice Number: ' . $invoice->invoice_number);
        $this->info('Client: ' . $client->name . ' (' . $client->email . ')');
        $this->info('Amount: ' . $invoice->total_amount);
        $this->info('Status: ' . $invoice->payment_status);
    }
}
