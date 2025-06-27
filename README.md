<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Distributor PostgreSQL API

API untuk sistem informasi distributor yang mengelola supplier dan produk.

## Overview

Sistem ini dirancang untuk distributor yang membeli produk dari supplier dalam skala besar (dus, karton, box) dan menjualnya kembali ke customer dengan margin keuntungan.

## Struktur Database

### Suppliers
- `id`: Primary key
- `nama`: Nama supplier
- `alamat`: Alamat supplier
- `nomor_telepon`: Nomor telepon supplier  
- `email`: Email supplier (unique)
- `timestamps`: created_at, updated_at

### Products
- `id`: Primary key
- `nama`: Nama produk
- `stok`: Jumlah stok (integer)
- `satuan`: Satuan produk (dus, karton, box, dll)
- `supplier_id`: Foreign key ke suppliers
- `harga_beli`: Harga beli dari supplier
- `harga_jual`: Harga jual ke customer
- `timestamps`: created_at, updated_at

## API Endpoints

Base URL: `http://localhost:8000/api`

### Suppliers API

#### GET /api/suppliers
Mengambil semua data supplier beserta produk-produknya.

**Response:**
```json
{
    "success": true,
    "message": "Data supplier berhasil diambil",
    "data": [
        {
            "id": 1,
            "nama": "PT Indofood Sukses Makmur",
            "alamat": "Jl. Sudirman Kav. 76-78, Jakarta Selatan",
            "nomor_telepon": "021-5795-8822",
            "email": "info@indofood.co.id",
            "products": [...]
        }
    ]
}
```

#### POST /api/suppliers
Menambah supplier baru.

**Request Body:**
```json
{
    "nama": "PT Supplier Baru",
    "alamat": "Jl. Contoh No. 123",
    "nomor_telepon": "021-1234567",
    "email": "supplier@email.com"
}
```

#### GET /api/suppliers/{id}
Mengambil detail supplier berdasarkan ID.

#### PUT/PATCH /api/suppliers/{id}
Mengupdate data supplier.

#### DELETE /api/suppliers/{id}
Menghapus supplier (hanya jika tidak memiliki produk).

### Products API

#### GET /api/products
Mengambil semua data produk beserta informasi supplier.

**Response:**
```json
{
    "success": true,
    "message": "Data produk berhasil diambil",
    "data": [
        {
            "id": 1,
            "nama": "Indomie Goreng Original",
            "stok": 500,
            "satuan": "dus",
            "supplier_id": 1,
            "harga_beli": "85000.00",
            "harga_jual": "95000.00",
            "supplier": {
                "id": 1,
                "nama": "PT Indofood Sukses Makmur",
                "alamat": "Jl. Sudirman Kav. 76-78, Jakarta Selatan",
                "nomor_telepon": "021-5795-8822",
                "email": "info@indofood.co.id"
            }
        }
    ]
}
```

#### POST /api/products
Menambah produk baru.

**Request Body:**
```json
{
    "nama": "Produk Baru",
    "stok": 100,
    "satuan": "dus",
    "supplier_id": 1,
    "harga_beli": 50000,
    "harga_jual": 60000
}
```

**Validasi:**
- Harga jual harus lebih besar dari harga beli
- Supplier harus exist
- Stok minimal 0

#### GET /api/products/{id}
Mengambil detail produk berdasarkan ID.

#### PUT/PATCH /api/products/{id}
Mengupdate data produk.

#### DELETE /api/products/{id}
Menghapus produk.

#### GET /api/suppliers/{supplier_id}/products
Mengambil semua produk dari supplier tertentu.

## Setup dan Instalasi

1. Clone repository
2. Install dependencies: `composer install`
3. Setup database di `.env`
4. Jalankan migration: `php artisan migrate`
5. Jalankan seeder: `php artisan db:seed`
6. Jalankan server: `php artisan serve`

## Testing API

Gunakan tools seperti Postman, Insomnia, atau curl untuk testing API endpoints.

Contoh testing dengan PowerShell:
```powershell
# Get all suppliers
Invoke-WebRequest -Uri "http://localhost:8000/api/suppliers" -Method GET

# Create new supplier
$body = @{
    nama = "PT Test Supplier"
    alamat = "Jl. Test No. 1"
    nomor_telepon = "021-9999999"
    email = "test@supplier.com"
} | ConvertTo-Json

Invoke-WebRequest -Uri "http://localhost:8000/api/suppliers" -Method POST -Body $body -ContentType "application/json"
```

## Fitur

- ✅ CRUD Suppliers
- ✅ CRUD Products  
- ✅ Relasi Supplier-Product
- ✅ Validasi business rules (harga jual > harga beli)
- ✅ Error handling yang comprehensive
- ✅ Response format yang konsisten
- ✅ Sample data untuk testing

## Business Rules

1. **Harga Jual > Harga Beli**: Sistem memastikan margin keuntungan selalu ada
2. **Supplier Integrity**: Supplier tidak bisa dihapus jika masih memiliki produk
3. **Stok Management**: Stok tidak boleh negatif
4. **Unique Email**: Email supplier harus unique
