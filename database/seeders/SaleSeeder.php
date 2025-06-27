<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Run ProductSeeder first.');
            return;
        }

        // Sample sales - penjualan ke customer
        $sales = [
            [
                'customer_name' => 'Toko Sari Barokah',
                'sale_date' => '2025-06-23',
                'items' => [
                    [
                        'product_name' => 'Indomie Goreng Original',
                        'quantity' => 20,
                        'unit_price' => 95000.00
                    ],
                    [
                        'product_name' => 'Milo Cokelat',
                        'quantity' => 5,
                        'unit_price' => 390000.00
                    ]
                ]
            ],
            [
                'customer_name' => 'Warung Mak Ijah',
                'sale_date' => '2025-06-24',
                'items' => [
                    [
                        'product_name' => 'Teh Pucuk Harum',
                        'quantity' => 10,
                        'unit_price' => 75000.00
                    ],
                    [
                        'product_name' => 'Sarimi Ayam Bawang',
                        'quantity' => 8,
                        'unit_price' => 52000.00
                    ]
                ]
            ],
            [
                'customer_name' => 'Minimarket Sejahtera',
                'sale_date' => '2025-06-26',
                'items' => [
                    [
                        'product_name' => 'Rinso Anti Noda',
                        'quantity' => 15,
                        'unit_price' => 135000.00
                    ],
                    [
                        'product_name' => 'Sunsilk Shampoo',
                        'quantity' => 12,
                        'unit_price' => 200000.00
                    ],
                    [
                        'product_name' => 'KitKat Chocolate',
                        'quantity' => 3,
                        'unit_price' => 280000.00
                    ]
                ]
            ],
            [
                'customer_name' => 'Toko Berkah Jaya',
                'sale_date' => '2025-06-27',
                'items' => [
                    [
                        'product_name' => 'Kopiko Coffee Shot',
                        'quantity' => 25,
                        'unit_price' => 95000.00
                    ],
                    [
                        'product_name' => 'Roma Kelapa',
                        'quantity' => 30,
                        'unit_price' => 85000.00
                    ]
                ]
            ]
        ];

        foreach ($sales as $saleData) {
            $totalAmount = 0;
            $saleItems = [];

            // Calculate total and prepare items - check stock availability
            foreach ($saleData['items'] as $item) {
                $product = $products->firstWhere('nama', $item['product_name']);
                if ($product && $product->stok >= $item['quantity']) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $subtotal;
                    
                    $saleItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal
                    ];
                }
            }

            if (!empty($saleItems)) {
                // Create sale
                $sale = Sale::create([
                    'customer_name' => $saleData['customer_name'],
                    'sale_date' => $saleData['sale_date'],
                    'total_amount' => $totalAmount
                ]);

                // Create sale items and decrease stock
                foreach ($saleItems as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal']
                    ]);

                    // Decrease product stock
                    $item['product']->decrement('stok', $item['quantity']);
                }
            }
        }
    }
}
