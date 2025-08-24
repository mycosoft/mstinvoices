<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Item;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Setting;
use Carbon\Carbon;

class KwefaakoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one if none exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]);
        }

        // Ensure settings exist for the user
        $settings = Setting::forUser($user->id);

        // Create Kwefaako Development Initiative client
        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'Kwefaako Development Initiative',
            'company_name' => 'Kwefaako Development Initiative',
            'email' => 'info@kwefaako.org',
            'phone' => '+256 700 000 000',
            'website' => 'https://kwefaako.org',
            'address' => 'Plot 123 Development Avenue',
            'city' => 'Kampala',
            'state' => 'Central',
            'postal_code' => '00256',
            'country' => 'Uganda',
            'business_type' => 'Non-Profit Organization',
            'status' => 'active',
            'notes' => 'Leading development organization focused on community empowerment and sustainable development projects.',
        ]);

        // Create some sample items
        $items = [
            [
                'name' => 'Website Development',
                'description' => 'Complete website development with modern design and CMS',
                'unit_price' => 2500.00,
                'unit_type' => 'project',
                'category' => 'Development',
                'sku' => 'WEB-DEV-001',
            ],
            [
                'name' => 'Training Workshop',
                'description' => 'Digital literacy training for community members',
                'unit_price' => 150.00,
                'unit_type' => 'day',
                'category' => 'Training',
                'sku' => 'TRAIN-001',
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Monthly technical support and maintenance',
                'unit_price' => 300.00,
                'unit_type' => 'month',
                'category' => 'Support',
                'sku' => 'SUPPORT-001',
            ],
        ];

        $createdItems = [];
        foreach ($items as $itemData) {
            $createdItems[] = Item::create(array_merge($itemData, [
                'user_id' => $user->id,
                'status' => 'active',
                'is_taxable' => true,
                'tax_rate' => 18.00, // Uganda VAT rate
            ]));
        }

        // Create a sample quotation
        $quotation = Quotation::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'quotation_number' => $settings->getNextQuotationNumber(),
            'reference_number' => 'KDI-REQ-2025-001',
            'quotation_date' => Carbon::today(),
            'valid_until' => Carbon::today()->addDays(30),
            'status' => 'draft',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'terms' => 'Payment terms: 50% advance, 50% on completion. Quotation valid for 30 days.',
            'footer' => 'Thank you for considering our services. We look forward to working with Kwefaako Development Initiative.',
            'notes' => 'Special NGO pricing applied. Includes training for staff members.',
            'currency' => 'UGX',
            'locale' => 'en_UG',
        ]);

        // Create quotation items
        $quotationItems = [
            [
                'item_id' => $createdItems[0]->id,
                'item_name' => $createdItems[0]->name,
                'item_description' => 'Complete website development including responsive design, content management system, donation portal, and blog functionality for Kwefaako Development Initiative.',
                'item_sku' => $createdItems[0]->sku,
                'unit_price' => 2500000.00, // UGX
                'quantity' => 1,
                'unit_type' => 'project',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'sort_order' => 1,
            ],
            [
                'item_id' => $createdItems[1]->id,
                'item_name' => $createdItems[1]->name,
                'item_description' => 'Digital literacy training workshops for community members including basic computer skills, internet usage, and online safety.',
                'item_sku' => $createdItems[1]->sku,
                'unit_price' => 150000.00, // UGX
                'quantity' => 5,
                'unit_type' => 'day',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'sort_order' => 2,
            ],
            [
                'item_id' => $createdItems[2]->id,
                'item_name' => $createdItems[2]->name,
                'item_description' => 'Monthly technical support and website maintenance including updates, backups, and security monitoring.',
                'item_sku' => $createdItems[2]->sku,
                'unit_price' => 300000.00, // UGX
                'quantity' => 6,
                'unit_type' => 'month',
                'is_taxable' => true,
                'tax_rate' => 18.00,
                'sort_order' => 3,
            ],
        ];

        foreach ($quotationItems as $itemData) {
            $quotationItem = QuotationItem::create(array_merge($itemData, [
                'quotation_id' => $quotation->id,
            ]));

            // Calculate line totals
            $lineTotal = $quotationItem->unit_price * $quotationItem->quantity;
            $taxAmount = $quotationItem->is_taxable ? ($lineTotal * $quotationItem->tax_rate / 100) : 0;
            $totalAmount = $lineTotal + $taxAmount;

            $quotationItem->update([
                'line_total' => $lineTotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }

        // Calculate quotation totals
        $quotation->calculateTotals();

        echo "✅ Kwefaako Development Initiative client created successfully!\n";
        echo "✅ Sample quotation {$quotation->quotation_number} created with 3 items\n";
        echo "✅ Total quotation value: {$settings->formatCurrency($quotation->total_amount)}\n";
        echo "\n📧 You can now test the quotation email functionality at:\n";
        echo "http://127.0.0.1:8000/quotations/{$quotation->id}\n";
    }
}
