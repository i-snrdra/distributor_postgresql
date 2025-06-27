<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="mt-2 text-gray-600">Ringkasan sistem informasi distributor</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Supplier</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.suppliers }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Produk</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.products }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Penjualan</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.sales }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Pembelian</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.purchases }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Sales -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Penjualan Terbaru</h3>
          <div v-if="recentSales.length > 0" class="flow-root">
            <ul class="-my-5 divide-y divide-gray-200">
              <li v-for="sale in recentSales" :key="sale.id" class="py-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ sale.customer_name }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(sale.sale_date) }}</p>
                  </div>
                  <div class="text-sm text-gray-900 font-medium">
                    Rp {{ formatCurrency(sale.total_amount) }}
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-gray-500">Belum ada data penjualan</p>
          </div>
        </div>
      </div>

      <!-- Recent Purchases -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pembelian Terbaru</h3>
          <div v-if="recentPurchases.length > 0" class="flow-root">
            <ul class="-my-5 divide-y divide-gray-200">
              <li v-for="purchase in recentPurchases" :key="purchase.id" class="py-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ purchase.supplier?.nama }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(purchase.purchase_date) }}</p>
                  </div>
                  <div class="text-sm text-gray-900 font-medium">
                    Rp {{ formatCurrency(purchase.total_amount) }}
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-gray-500">Belum ada data pembelian</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
      <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Aksi Cepat</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link to="/suppliers" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center">
          <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Kelola Supplier</p>
        </router-link>

        <router-link to="/products" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center">
          <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Kelola Produk</p>
        </router-link>

        <router-link to="/sales" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center">
          <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Transaksi Penjualan</p>
        </router-link>

        <router-link to="/purchases" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center">
          <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Transaksi Pembelian</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { suppliersAPI, productsAPI, salesAPI, purchasesAPI } from '../services/api'

export default {
  name: 'Dashboard',
  setup() {
    const stats = ref({
      suppliers: 0,
      products: 0,
      sales: 0,
      purchases: 0
    })
    
    const recentSales = ref([])
    const recentPurchases = ref([])

    const loadDashboardData = async () => {
      try {
        // Load stats
        const [suppliersRes, productsRes, salesRes, purchasesRes] = await Promise.all([
          suppliersAPI.getAll(),
          productsAPI.getAll(),
          salesAPI.getAll(),
          purchasesAPI.getAll()
        ])

        stats.value = {
          suppliers: suppliersRes.data.data.length,
          products: productsRes.data.data.length,
          sales: salesRes.data.data.length,
          purchases: purchasesRes.data.data.length
        }

        // Get recent transactions (last 5)
        recentSales.value = salesRes.data.data.slice(0, 5)
        recentPurchases.value = purchasesRes.data.data.slice(0, 5)
      } catch (error) {
        console.error('Error loading dashboard data:', error)
      }
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID').format(amount)
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      stats,
      recentSales,
      recentPurchases,
      formatCurrency,
      formatDate
    }
  }
}
</script> 