<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        
        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->info('No suppliers or products found. Run SupplierSeeder and ProductSeeder first.');
            return;
        }

        // Sample purchases - pembelian stok tambahan
        $purchases = [
            [
                'supplier_id' => $suppliers->where('nama', 'PT Indofood Sukses Makmur')->first()->id,
                'purchase_date' => '2025-06-20',
                'items' => [
                    [
                        'product_name' => 'Indomie Goreng Original',
                        'quantity' => 100,
                        'unit_price' => 85000.00
                    ],
                    [
                        'product_name' => 'Sarimi Ayam Bawang',
                        'quantity' => 50,
                        'unit_price' => 45000.00
                    ]
                ]
            ],
            [
                'supplier_id' => $suppliers->where('nama', 'PT Unilever Indonesia')->first()->id,
                'purchase_date' => '2025-06-22',
                'items' => [
                    [
                        'product_name' => 'Rinso Anti Noda',
                        'quantity' => 30,
                        'unit_price' => 120000.00
                    ]
                ]
            ],
            [
                'supplier_id' => $suppliers->where('nama', 'PT Wings Surya')->first()->id,
                'purchase_date' => '2025-06-25',
                'items' => [
                    [
                        'product_name' => 'Teh Pucuk Harum',
                        'quantity' => 200,
                        'unit_price' => 65000.00
                    ],
                    [
                        'product_name' => 'So Klin Softergent',
                        'quantity' => 75,
                        'unit_price' => 95000.00
                    ]
                ]
            ]
        ];

        foreach ($purchases as $purchaseData) {
            $totalAmount = 0;
            $purchaseItems = [];

            // Calculate total and prepare items
            foreach ($purchaseData['items'] as $item) {
                $product = $products->firstWhere('nama', $item['product_name']);
                if ($product && $product->supplier_id == $purchaseData['supplier_id']) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $subtotal;
                    
                    $purchaseItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal
                    ];
                }
            }

            if (!empty($purchaseItems)) {
                // Create purchase
                $purchase = Purchase::create([
                    'supplier_id' => $purchaseData['supplier_id'],
                    'purchase_date' => $purchaseData['purchase_date'],
                    'total_amount' => $totalAmount
                ]);

                // Create purchase items and increase stock
                foreach ($purchaseItems as $item) {
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Increase product stock
                    $item['product']->increment('stok', $item['quantity']);
                }
            }
        }
    }
}
