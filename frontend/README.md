# Frontend Vue.js - Sistem Informasi Distributor

Frontend aplikasi berbasis Vue.js 3 dengan Composition API untuk Sistem Informasi Distributor.

## 🚀 Fitur

- **Dashboard** - Overview statistik dan data terbaru
- **Manajemen Supplier** - CRUD supplier dengan validasi email unik
- **Manajemen Produk** - CRUD produk dengan validasi harga dan stok
- **Transaksi Penjualan** - Multi-item transactions dengan stock management
- **Transaksi Pembelian** - Multi-item transactions dengan supplier validation
- **Responsive Design** - Menggunakan Tailwind CSS
- **Real-time Stock Management** - Otomatis update stok saat transaksi

## 🛠️ Tech Stack

- **Vue.js 3** - Framework JavaScript
- **Vue Router 4** - Routing
- **Axios** - HTTP Client untuk API calls
- **Tailwind CSS** - Styling framework
- **Vite** - Build tool
- **Heroicons** - Icon set

## 📋 Prerequisites

- Node.js (v16 atau lebih baru)
- npm atau yarn
- Laravel API backend harus berjalan di `http://localhost:8000`

## 🔧 Installation

1. **Masuk ke direktori frontend:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Jalankan development server:**
   ```bash
   npm run dev
   ```

4. **Akses aplikasi:**
   - URL: http://localhost:3000

## 📝 Scripts

```bash
# Development server
npm run dev

# Build untuk production
npm run build

# Preview production build
npm run preview
```

## 🔗 API Endpoints

Aplikasi ini mengonsumsi API Laravel dengan endpoints:

### Suppliers
- `GET /api/suppliers` - Get all suppliers
- `GET /api/suppliers/{id}` - Get supplier by ID
- `POST /api/suppliers` - Create new supplier
- `PUT /api/suppliers/{id}` - Update supplier
- `DELETE /api/suppliers/{id}` - Delete supplier
- `GET /api/suppliers/{id}/products` - Get products by supplier

### Products
- `GET /api/products` - Get all products
- `GET /api/products/{id}` - Get product by ID
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Sales
- `GET /api/sales` - Get all sales
- `GET /api/sales/{id}` - Get sale by ID
- `POST /api/sales` - Create new sale transaction
- `PUT /api/sales/{id}` - Update sale transaction
- `DELETE /api/sales/{id}` - Delete sale transaction

### Purchases
- `GET /api/purchases` - Get all purchases
- `GET /api/purchases/{id}` - Get purchase by ID
- `POST /api/purchases` - Create new purchase transaction
- `PUT /api/purchases/{id}` - Update purchase transaction
- `DELETE /api/purchases/{id}` - Delete purchase transaction

## 🎨 Components Structure

```
src/
├── components/           # Reusable components
├── views/               # Page components
│   ├── Dashboard.vue    # Homepage dengan statistik
│   ├── Suppliers.vue    # Manajemen supplier
│   ├── Products.vue     # Manajemen produk
│   ├── Sales.vue        # Transaksi penjualan
│   └── Purchases.vue    # Transaksi pembelian
├── services/
│   └── api.js          # API service layer
├── router/
│   └── index.js        # Vue Router configuration
├── style.css           # Global styles dengan Tailwind
├── App.vue             # Root component
└── main.js             # Entry point
```

## 🎯 Key Features

### 1. Dashboard
- Statistik real-time (Total Supplier, Produk, Penjualan, Pembelian)
- Recent transactions display
- Quick action buttons

### 2. Supplier Management
- CRUD operations
- Email validation
- Product count display
- Bulk operations

### 3. Product Management
- CRUD operations
- Supplier relationship
- Stock level indicators (Red: 0, Yellow: <10, Green: >=10)
- Price validation (selling price > purchase price)

### 4. Sales Transactions
- Multi-item support
- Real-time stock validation
- Automatic stock reduction
- Customer information
- Total calculation

### 5. Purchase Transactions
- Multi-item support from same supplier
- Automatic stock increase
- Supplier-product validation
- Total calculation

## 🔧 Configuration

### API Base URL
Edit `src/services/api.js` untuk mengubah base URL API:

```javascript
const API_BASE_URL = 'http://localhost:8000/api'
```

### Styling
Customization Tailwind CSS di `tailwind.config.js`:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        50: '#eff6ff',
        500: '#3b82f6',
        600: '#2563eb',
        700: '#1d4ed8',
      }
    }
  },
}
```

## 🚨 Error Handling

- Network errors ditampilkan dengan alert
- Validation errors dari API ditampilkan dengan pesan yang jelas
- Loading states untuk better UX
- Form validation sebelum submit

## 📱 Responsive Design

- Mobile-first approach
- Responsive navigation dengan hamburger menu
- Adaptive table layouts
- Touch-friendly interface

## 🔒 Business Rules Implemented

1. **Suppliers**: Email harus unik, tidak bisa dihapus jika masih ada produk
2. **Products**: Harga jual harus lebih besar dari harga beli
3. **Sales**: Quantity tidak boleh melebihi stok tersedia
4. **Purchases**: Produk harus dari supplier yang dipilih
5. **Stock Management**: Otomatis update saat transaksi

## 🧪 Testing

Untuk testing manual:
1. Buka aplikasi di browser
2. Test setiap CRUD operation
3. Coba validasi error scenarios
4. Test responsive design di berbagai device

## 📞 Support

Jika ada masalah atau pertanyaan, pastikan:
1. Laravel API backend sudah berjalan
2. Database sudah di-migrate dan di-seed
3. CORS sudah dikonfigurasi di Laravel
4. Port 3000 tidak bentrok dengan aplikasi lain 