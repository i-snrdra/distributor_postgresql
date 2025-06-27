<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'nama' => 'PT Indofood Sukses Makmur',
                'alamat' => 'Jl. Sudirman Kav. 76-78, Jakarta Selatan',
                'nomor_telepon' => '021-5795-8822',
                'email' => 'info@indofood.co.id'
            ],
            [
                'nama' => 'PT Unilever Indonesia',
                'alamat' => 'Jl. BSD Boulevard Barat, Tangerang',
                'nomor_telepon' => '021-5460-0000',
                'email' => 'contact@unilever.co.id'
            ],
            [
                'nama' => 'PT Nestle Indonesia',
                'alamat' => 'Jl. Raya Perjuangan No. 8, Jakarta Barat',
                'nomor_telepon' => '021-5450-7000',
                'email' => 'info@nestle.co.id'
            ],
            [
                'nama' => 'PT Wings Surya',
                'alamat' => 'Jl. Raya Surabaya-Malang Km. 45, Surabaya',
                'nomor_telepon' => '031-7997-777',
                'email' => 'info@wings.co.id'
            ],
            [
                'nama' => 'PT Mayora Indah',
                'alamat' => 'Jl. Tomang Raya No. 21-23, Jakarta Barat',
                'nomor_telepon' => '021-566-7575',
                'email' => 'corporate@mayora.com'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
