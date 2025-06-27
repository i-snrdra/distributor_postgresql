<template>
  <div>
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Transaksi Penjualan
        </h1>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button @click="openModal()" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Penjualan
        </button>
      </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>
      
      <div v-else-if="sales.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi penjualan</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan transaksi penjualan baru</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Customer
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tanggal
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Items
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="sale in sales" :key="sale.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ sale.customer_name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(sale.sale_date) }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">
                <div v-for="item in sale.items" :key="item.id" class="mb-1">
                  {{ item.product?.nama }} ({{ item.quantity }} {{ item.product?.satuan }})
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                Rp {{ formatCurrency(sale.total_amount) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="viewSale(sale)" class="text-blue-600 hover:text-blue-900 mr-3">
                Detail
              </button>
              <button @click="editSale(sale)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                Edit
              </button>
              <button @click="deleteSale(sale)" class="text-red-600 hover:text-red-900">
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEditing ? 'Edit Penjualan' : 'Tambah Penjualan Baru' }}
          </h3>
          
          <form @submit.prevent="saveSale">
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <label class="form-label">Nama Customer</label>
                <input v-model="form.customer_name" type="text" class="form-input" required>
              </div>
              <div>
                <label class="form-label">Tanggal Penjualan</label>
                <input v-model="form.sale_date" type="date" class="form-input" required>
              </div>
            </div>

            <!-- Items Section -->
            <div class="mb-6">
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">Items Penjualan</h4>
                <button type="button" @click="addItem" class="btn-success">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Tambah Item
                </button>
              </div>

              <div v-for="(item, index) in form.items" :key="index" class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <label class="form-label">Produk</label>
                    <select v-model="item.product_id" @change="updateItemPrice(index)" class="form-input" required>
                      <option value="">Pilih Produk</option>
                      <option v-for="product in products" :key="product.id" :value="product.id">
                        {{ product.nama }} (Stok: {{ product.stok }})
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="form-label">Quantity</label>
                    <input v-model.number="item.quantity" @input="calculateSubtotal(index)" type="number" min="1" class="form-input" required>
                  </div>
                  <div>
                    <label class="form-label">Harga Satuan</label>
                    <input v-model.number="item.unit_price" @input="calculateSubtotal(index)" type="number" min="0" class="form-input" required>
                  </div>
                  <div>
                    <label class="form-label">Subtotal</label>
                    <input :value="formatCurrency(item.subtotal || 0)" type="text" class="form-input bg-gray-100" readonly>
                    <button type="button" @click="removeItem(index)" class="mt-2 btn-danger w-full">
                      Hapus
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
              <div class="text-right">
                <span class="text-lg font-medium text-gray-900">
                  Total: Rp {{ formatCurrency(calculateTotal()) }}
                </span>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button type="button" @click="closeModal" class="btn-secondary">
                Batal
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting || form.items.length === 0">
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-3/5 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penjualan</h3>
          
          <div v-if="selectedSale" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <strong>Customer:</strong> {{ selectedSale.customer_name }}
              </div>
              <div>
                <strong>Tanggal:</strong> {{ formatDate(selectedSale.sale_date) }}
              </div>
            </div>

            <div>
              <strong>Items:</strong>
              <table class="min-w-full mt-2 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in selectedSale.items" :key="item.id">
                    <td class="px-4 py-2">{{ item.product?.nama }}</td>
                    <td class="px-4 py-2">{{ item.quantity }}</td>
                    <td class="px-4 py-2">Rp {{ formatCurrency(item.unit_price) }}</td>
                    <td class="px-4 py-2">Rp {{ formatCurrency(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="text-right font-bold">
              Total: Rp {{ formatCurrency(selectedSale.total_amount) }}
            </div>
          </div>
          
          <div class="flex justify-end mt-6">
            <button @click="closeDetailModal" class="btn-secondary">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { salesAPI, productsAPI } from '../services/api'

export default {
  name: 'Sales',
  setup() {
    const sales = ref([])
    const products = ref([])
    const loading = ref(false)
    const showModal = ref(false)
    const showDetailModal = ref(false)
    const isEditing = ref(false)
    const submitting = ref(false)
    const editingId = ref(null)
    const selectedSale = ref(null)
    
    const form = ref({
      customer_name: '',
      sale_date: '',
      items: []
    })

    const loadSales = async () => {
      loading.value = true
      try {
        const response = await salesAPI.getAll()
        sales.value = response.data.data
      } catch (error) {
        console.error('Error loading sales:', error)
        alert('Error loading sales: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
      }
    }

    const loadProducts = async () => {
      try {
        const response = await productsAPI.getAll()
        products.value = response.data.data
      } catch (error) {
        console.error('Error loading products:', error)
      }
    }

    const openModal = () => {
      showModal.value = true
      isEditing.value = false
      resetForm()
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const closeDetailModal = () => {
      showDetailModal.value = false
      selectedSale.value = null
    }

    const resetForm = () => {
      form.value = {
        customer_name: '',
        sale_date: new Date().toISOString().split('T')[0],
        items: [{ product_id: '', quantity: 1, unit_price: 0, subtotal: 0 }]
      }
      editingId.value = null
    }

    const addItem = () => {
      form.value.items.push({
        product_id: '',
        quantity: 1,
        unit_price: 0,
        subtotal: 0
      })
    }

    const removeItem = (index) => {
      form.value.items.splice(index, 1)
    }

    const updateItemPrice = (index) => {
      const item = form.value.items[index]
      const product = products.value.find(p => p.id == item.product_id)
      if (product) {
        item.unit_price = product.harga_jual
        calculateSubtotal(index)
      }
    }

    const calculateSubtotal = (index) => {
      const item = form.value.items[index]
      item.subtotal = item.quantity * item.unit_price
    }

    const calculateTotal = () => {
      return form.value.items.reduce((total, item) => total + (item.subtotal || 0), 0)
    }

    const viewSale = (sale) => {
      selectedSale.value = sale
      showDetailModal.value = true
    }

    const editSale = (sale) => {
      form.value = {
        customer_name: sale.customer_name,
        sale_date: sale.sale_date,
        items: sale.items.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          unit_price: item.unit_price,
          subtotal: item.subtotal
        }))
      }
      editingId.value = sale.id
      isEditing.value = true
      showModal.value = true
    }

    const saveSale = async () => {
      // Validate stock availability
      for (const item of form.value.items) {
        const product = products.value.find(p => p.id == item.product_id)
        if (product && item.quantity > product.stok) {
          alert(`Stok ${product.nama} tidak mencukupi! Stok tersedia: ${product.stok}`)
          return
        }
      }

      submitting.value = true
      try {
        if (isEditing.value) {
          await salesAPI.update(editingId.value, form.value)
        } else {
          await salesAPI.create(form.value)
        }
        closeModal()
        loadSales()
        loadProducts() // Refresh products to update stock
        alert(isEditing.value ? 'Penjualan berhasil diperbarui!' : 'Penjualan berhasil ditambahkan!')
      } catch (error) {
        console.error('Error saving sale:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      } finally {
        submitting.value = false
      }
    }

    const deleteSale = async (sale) => {
      if (!confirm(`Apakah Anda yakin ingin menghapus penjualan untuk "${sale.customer_name}"?`)) {
        return
      }

      try {
        await salesAPI.delete(sale.id)
        loadSales()
        loadProducts() // Refresh products to update stock
        alert('Penjualan berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting sale:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
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
      loadSales()
      loadProducts()
    })

    return {
      sales,
      products,
      loading,
      showModal,
      showDetailModal,
      isEditing,
      submitting,
      selectedSale,
      form,
      openModal,
      closeModal,
      closeDetailModal,
      addItem,
      removeItem,
      updateItemPrice,
      calculateSubtotal,
      calculateTotal,
      viewSale,
      editSale,
      saveSale,
      deleteSale,
      formatCurrency,
      formatDate
    }
  }
}
</script> 