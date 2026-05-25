<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItInventorySeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Admin user

        // Ensure user exists
        $user = \App\Models\User::find($userId);
        if (!$user) {
            $this->command->error('User ID 1 not found. Run DatabaseSeeder first.');
            return;
        }

        $this->command->info('Seeding IT inventory data...');

        // ==========================================
        // 1. Create Main Warehouse
        // ==========================================
        $warehouse = Warehouse::firstOrCreate(
            ['user_id' => $userId, 'code' => 'MAIN'],
            [
                'name' => 'Main Store',
                'address' => 'Kampala Road, Plot 15',
                'is_default' => true,
                'status' => 'active',
            ]
        );
        $this->command->info('✅ Warehouse: Main Store');

        // ==========================================
        // 2. Create Suppliers
        // ==========================================
        $suppliers = [];

        $suppliers[] = Supplier::create([
            'user_id' => $userId,
            'name' => 'Charles Mugisha',
            'company_name' => 'Jumia Uganda Ltd',
            'email' => 'orders@jumia.ug',
            'phone' => '+256 700 123 456',
            'address' => 'Plot 17, Jinja Road',
            'city' => 'Kampala',
            'state' => 'Central',
            'country' => 'Uganda',
            'tax_number' => 'UG-12345-6789',
            'payment_terms' => 30,
            'status' => 'active',
            'notes' => 'Major IT equipment supplier - laptops, phones, accessories',
        ]);

        $suppliers[] = Supplier::create([
            'user_id' => $userId,
            'name' => 'Sarah Nakato',
            'company_name' => 'CompuTech Solutions (U) Ltd',
            'email' => 'sales@computechnologies.co.ug',
            'phone' => '+256 772 987 654',
            'address' => 'Plot 8, Lumumba Avenue',
            'city' => 'Kampala',
            'state' => 'Central',
            'country' => 'Uganda',
            'tax_number' => 'UG-98765-4321',
            'payment_terms' => 15,
            'status' => 'active',
            'notes' => 'Computer hardware, RAM, storage, and peripherals',
        ]);

        $this->command->info('✅ 2 Suppliers created (Jumia Uganda Ltd, CompuTech Solutions)');

        // ==========================================
        // 3. Create Products/Items
        // ==========================================
        $items = [];

        $itemsData = [
            // Laptops
            [
                'name' => 'HP EliteBook 840 G6',
                'description' => 'Intel Core i7-8665U, 16GB RAM, 512GB SSD, 14" FHD, Win 11 Pro',
                'category' => 'Laptops',
                'unit_price' => 3200000,
                'cost_price' => 2500000,
                'track_inventory' => true,
                'stock_quantity' => 8,
                'low_stock_alert' => 2,
                'reorder_point' => 3,
                'reorder_quantity' => 5,
            ],
            [
                'name' => 'Dell Latitude 5520',
                'description' => 'Intel Core i5-1145G7, 8GB RAM, 256GB SSD, 15.6" FHD, Win 11 Pro',
                'category' => 'Laptops',
                'unit_price' => 2800000,
                'cost_price' => 2200000,
                'track_inventory' => true,
                'stock_quantity' => 5,
                'low_stock_alert' => 2,
                'reorder_point' => 3,
                'reorder_quantity' => 4,
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 9',
                'description' => 'Intel Core i7-1165G7, 16GB RAM, 512GB SSD, 14" 2K IPS, Win 11 Pro',
                'category' => 'Laptops',
                'unit_price' => 4500000,
                'cost_price' => 3600000,
                'track_inventory' => true,
                'stock_quantity' => 3,
                'low_stock_alert' => 1,
                'reorder_point' => 2,
                'reorder_quantity' => 3,
            ],

            // Smartphones
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Snapdragon 8 Gen 3, 12GB RAM, 256GB, 200MP Camera, 5G, S Pen',
                'category' => 'Smartphones',
                'unit_price' => 4500000,
                'cost_price' => 3800000,
                'track_inventory' => true,
                'stock_quantity' => 6,
                'low_stock_alert' => 2,
                'reorder_point' => 3,
                'reorder_quantity' => 4,
            ],
            [
                'name' => 'Samsung Galaxy A55 5G',
                'description' => 'Exynos 1480, 8GB RAM, 128GB, 50MP Camera, 6.6" Super AMOLED',
                'category' => 'Smartphones',
                'unit_price' => 1800000,
                'cost_price' => 1450000,
                'track_inventory' => true,
                'stock_quantity' => 12,
                'low_stock_alert' => 3,
                'reorder_point' => 5,
                'reorder_quantity' => 10,
            ],
            [
                'name' => 'Infinix GT 20 Pro',
                'description' => 'Dimensity 8200, 12GB RAM, 256GB, 108MP Camera, 5G, 120Hz AMOLED',
                'category' => 'Smartphones',
                'unit_price' => 1500000,
                'cost_price' => 1200000,
                'track_inventory' => true,
                'stock_quantity' => 15,
                'low_stock_alert' => 3,
                'reorder_point' => 5,
                'reorder_quantity' => 10,
            ],
            [
                'name' => 'Infinix Note 40 Pro',
                'description' => 'Helio G99 Ultimate, 8GB RAM, 256GB, 108MP Camera, 120Hz AMOLED',
                'category' => 'Smartphones',
                'unit_price' => 950000,
                'cost_price' => 750000,
                'track_inventory' => true,
                'stock_quantity' => 20,
                'low_stock_alert' => 5,
                'reorder_point' => 8,
                'reorder_quantity' => 15,
            ],
            [
                'name' => 'Redmi Note 13 Pro+',
                'description' => 'Dimensity 7200-Ultra, 8GB RAM, 256GB, 200MP Camera, 120W Charging',
                'category' => 'Smartphones',
                'unit_price' => 1600000,
                'cost_price' => 1300000,
                'track_inventory' => true,
                'stock_quantity' => 10,
                'low_stock_alert' => 3,
                'reorder_point' => 5,
                'reorder_quantity' => 8,
            ],
            [
                'name' => 'Tecno Camon 30 Premier',
                'description' => 'Dimensity 8200, 12GB RAM, 512GB, 50MP Periscope Camera, 4K Video',
                'category' => 'Smartphones',
                'unit_price' => 1400000,
                'cost_price' => 1100000,
                'track_inventory' => true,
                'stock_quantity' => 8,
                'low_stock_alert' => 2,
                'reorder_point' => 4,
                'reorder_quantity' => 6,
            ],
            [
                'name' => 'Tecno Spark 20 Pro',
                'description' => 'Helio G99, 8GB RAM, 256GB, 108MP Camera, 6.8" 120Hz Display',
                'category' => 'Smartphones',
                'unit_price' => 650000,
                'cost_price' => 500000,
                'track_inventory' => true,
                'stock_quantity' => 25,
                'low_stock_alert' => 5,
                'reorder_point' => 10,
                'reorder_quantity' => 20,
            ],

            // Computer Accessories
            [
                'name' => 'Corsair Vengeance 16GB DDR4 RAM',
                'description' => '16GB (1x16GB) DDR4 3200MHz Desktop RAM, CL16',
                'category' => 'Computer Accessories',
                'unit_price' => 180000,
                'cost_price' => 130000,
                'track_inventory' => true,
                'stock_quantity' => 30,
                'low_stock_alert' => 5,
                'reorder_point' => 10,
                'reorder_quantity' => 20,
            ],
            [
                'name' => 'HP V220 Wired Keyboard',
                'description' => 'HP V220 USB Wired Keyboard, Full-size, Spill-resistant, Black',
                'category' => 'Computer Accessories',
                'unit_price' => 65000,
                'cost_price' => 45000,
                'track_inventory' => true,
                'stock_quantity' => 40,
                'low_stock_alert' => 10,
                'reorder_point' => 15,
                'reorder_quantity' => 25,
            ],
            [
                'name' => 'Logitech M90 Optical Mouse',
                'description' => 'Logitech B100 USB Optical Mouse, 3 Buttons, Scroll Wheel, Black',
                'category' => 'Computer Accessories',
                'unit_price' => 45000,
                'cost_price' => 30000,
                'track_inventory' => true,
                'stock_quantity' => 50,
                'low_stock_alert' => 10,
                'reorder_point' => 20,
                'reorder_quantity' => 30,
            ],
            [
                'name' => 'Samsung 870 EVO 500GB SSD',
                'description' => 'Samsung 870 EVO 500GB 2.5" SATA III SSD, Read 560MB/s, Write 530MB/s',
                'category' => 'Computer Accessories',
                'unit_price' => 280000,
                'cost_price' => 210000,
                'track_inventory' => true,
                'stock_quantity' => 15,
                'low_stock_alert' => 3,
                'reorder_point' => 5,
                'reorder_quantity' => 10,
            ],
        ];

        foreach ($itemsData as $data) {
            $items[] = Item::create(array_merge($data, [
                'user_id' => $userId,
                'unit_type' => 'piece',
                'is_taxable' => true,
                'tax_rate' => 18,
                'is_service' => false,
                'status' => 'active',
                'warehouse_location' => $warehouse->code,
            ]));
        }

        $this->command->info('✅ ' . count($items) . ' IT Products created');

        // ==========================================
        // 4. Create Purchase Orders with stock
        // ==========================================
        $this->command->info('Creating purchase orders...');

        // --- Purchase Order 1: Jumia Uganda - Laptops & Smartphones ---
        $purchase1Items = [
            ['item' => $items[0], 'qty' => 5, 'cost' => 2500000], // HP EliteBook
            ['item' => $items[1], 'qty' => 3, 'cost' => 2200000], // Dell Latitude
            ['item' => $items[4], 'qty' => 8, 'cost' => 1450000], // Samsung A55
            ['item' => $items[5], 'qty' => 10, 'cost' => 1200000], // Infinix GT 20 Pro
            ['item' => $items[8], 'qty' => 6, 'cost' => 1100000], // Tecno Camon 30
        ];

        $this->createPurchase($userId, $suppliers[0]->id, $warehouse->id, 'PUR-2026-0001', $purchase1Items, now()->subDays(14), now()->subDays(12), 'received', 'paid', 'Initial stock order - Laptops & high-end smartphones');

        // --- Purchase Order 2: CompuTech - Accessories & Budget Phones ---
        $purchase2Items = [
            ['item' => $items[7], 'qty' => 8, 'cost' => 1300000], // Redmi Note 13 Pro+
            ['item' => $items[9], 'qty' => 15, 'cost' => 500000],  // Tecno Spark 20 Pro
            ['item' => $items[6], 'qty' => 12, 'cost' => 750000],  // Infinix Note 40 Pro
            ['item' => $items[10], 'qty' => 20, 'cost' => 130000], // Corsair RAM
            ['item' => $items[3], 'qty' => 4, 'cost' => 3800000],  // Samsung S24 Ultra
        ];

        $this->createPurchase($userId, $suppliers[1]->id, $warehouse->id, 'PUR-2026-0002', $purchase2Items, now()->subDays(7), now()->subDays(5), 'received', 'partial', 'Stock replenishment - phones & accessories');

        // --- Purchase Order 3: CompuTech - Peripherals ---
        $purchase3Items = [
            ['item' => $items[11], 'qty' => 25, 'cost' => 45000],  // HP Keyboards
            ['item' => $items[12], 'qty' => 30, 'cost' => 30000],  // Logitech Mice
            ['item' => $items[13], 'qty' => 10, 'cost' => 210000], // Samsung SSDs
            ['item' => $items[2], 'qty' => 2, 'cost' => 3600000],  // Lenovo ThinkPad
        ];

        $this->createPurchase($userId, $suppliers[1]->id, $warehouse->id, 'PUR-2026-0003', $purchase3Items, now()->subDays(3), now()->subDays(1), 'received', 'unpaid', 'Peripherals and high-end laptop restock');

        $this->command->info('✅ 3 Purchase Orders created with stock movements');
        $this->command->info('');
        $this->command->info('🎉 IT Inventory seeding complete!');
    }

    private function createPurchase(int $userId, int $supplierId, int $warehouseId, string $purchaseNumber, array $items, $orderDate, $deliveryDate, string $status, string $paymentStatus, string $notes): Purchase
    {
        $subtotal = 0;
        foreach ($items as $itemData) {
            $subtotal += $itemData['cost'] * $itemData['qty'];
        }

        $taxRate = 18;
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;

        $paidAmount = match ($paymentStatus) {
            'paid' => $totalAmount,
            'partial' => $totalAmount * 0.5,
            default => 0,
        };

        $purchase = Purchase::create([
            'user_id' => $userId,
            'supplier_id' => $supplierId,
            'purchase_number' => $purchaseNumber,
            'reference' => 'PO-' . $purchaseNumber,
            'purchase_date' => $orderDate,
            'delivery_date' => $deliveryDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_due' => $totalAmount - $paidAmount,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'warehouse_id' => $warehouseId,
            'notes' => $notes,
            'terms' => 'Net 30',
        ]);

        // Create purchase items and stock movements
        foreach ($items as $itemData) {
            $item = $itemData['item'];
            $qty = $itemData['qty'];
            $cost = $itemData['cost'];
            $lineTotal = $cost * $qty;

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $item->id,
                'item_name' => $item->name,
                'description' => $item->description,
                'unit_cost' => $cost,
                'quantity' => $qty,
                'unit_type' => 'piece',
                'line_total' => $lineTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotal * ($taxRate / 100),
                'discount_amount' => 0,
                'total_amount' => $lineTotal + ($lineTotal * ($taxRate / 100)),
            ]);

            // Create stock movement (stock in)
            StockMovement::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'warehouse_id' => $warehouseId,
                'type' => 'in',
                'quantity' => $qty,
                'unit_cost' => $cost,
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
                'notes' => 'Purchase order: ' . $purchaseNumber,
                'movement_date' => $deliveryDate,
            ]);
        }

        // Create payment record if paid
        if ($paidAmount > 0) {
            PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'amount' => $paidAmount,
                'payment_date' => $deliveryDate,
                'payment_method' => 'bank_transfer',
                'reference' => 'TXN-' . $purchaseNumber,
                'notes' => 'Payment for ' . $purchaseNumber,
                'created_by' => $userId,
            ]);
        }

        return $purchase;
    }
}
