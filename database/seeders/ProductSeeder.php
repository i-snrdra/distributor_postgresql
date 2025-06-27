<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        
        if ($suppliers->isEmpty()) {
            $this->command->info('No suppliers found. Run SupplierSeeder first.');
            return;
        }

        $products = [
            // Produk Indofood
            [
                'nama' => 'Indomie Goreng Original',
                'stok' => 500,
                'satuan' => 'dus',
                'supplier_id' => $suppliers->where('nama', 'PT Indofood Sukses Makmur')->first()->id,
                'harga_beli' => 85000.00,
                'harga_jual' => 95000.00
            ],
            [
                'nama' => 'Sarimi Ayam Bawang',
                'stok' => 300,
                'satuan' => 'karton',
                'supplier_id' => $suppliers->where('nama', 'PT Indofood Sukses Makmur')->first()->id,
                'harga_beli' => 45000.00,
                'harga_jual' => 52000.00
            ],
            
            // Produk Unilever
            [
                'nama' => 'Rinso Anti Noda',
                'stok' => 200,
                'satuan' => 'box',
                'supplier_id' => $suppliers->where('nama', 'PT Unilever Indonesia')->first()->id,
                'harga_beli' => 120000.00,
                'harga_jual' => 135000.00
            ],
            [
                'nama' => 'Sunsilk Shampoo',
                'stok' => 150,
                'satuan' => 'karton',
                'supplier_id' => $suppliers->where('nama', 'PT Unilever Indonesia')->first()->id,
                'harga_beli' => 180000.00,
                'harga_jual' => 200000.00
            ],
            
            // Produk Nestle
            [
                'nama' => 'Milo Cokelat',
                'stok' => 100,
                'satuan' => 'dus',
                'supplier_id' => $suppliers->where('nama', 'PT Nestle Indonesia')->first()->id,
                'harga_beli' => 350000.00,
                'harga_jual' => 390000.00
            ],
            [
                'nama' => 'KitKat Chocolate',
                'stok' => 80,
                'satuan' => 'box',
                'supplier_id' => $suppliers->where('nama', 'PT Nestle Indonesia')->first()->id,
                'harga_beli' => 250000.00,
                'harga_jual' => 280000.00
            ],
            
            // Produk Wings
            [
                'nama' => 'So Klin Softergent',
                'stok' => 250,
                'satuan' => 'karton',
                'supplier_id' => $suppliers->where('nama', 'PT Wings Surya')->first()->id,
                'harga_beli' => 95000.00,
                'harga_jual' => 110000.00
            ],
            [
                'nama' => 'Teh Pucuk Harum',
                'stok' => 400,
                'satuan' => 'dus',
                'supplier_id' => $suppliers->where('nama', 'PT Wings Surya')->first()->id,
                'harga_beli' => 65000.00,
                'harga_jual' => 75000.00
            ],
            
            // Produk Mayora
            [
                'nama' => 'Kopiko Coffee Shot',
                'stok' => 180,
                'satuan' => 'box',
                'supplier_id' => $suppliers->where('nama', 'PT Mayora Indah')->first()->id,
                'harga_beli' => 85000.00,
                'harga_jual' => 95000.00
            ],
            [
                'nama' => 'Roma Kelapa',
                'stok' => 220,
                'satuan' => 'karton',
                'supplier_id' => $suppliers->where('nama', 'PT Mayora Indah')->first()->id,
                'harga_beli' => 75000.00,
                'harga_jual' => 85000.00
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
