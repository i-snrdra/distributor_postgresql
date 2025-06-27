<template>
  <div>
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Transaksi Pembelian
        </h1>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button @click="openModal()" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Pembelian
        </button>
      </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>
      
      <div v-else-if="purchases.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi pembelian</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan transaksi pembelian baru</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Supplier
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
          <tr v-for="purchase in purchases" :key="purchase.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ purchase.supplier?.nama }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(purchase.purchase_date) }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">
                <div v-for="item in purchase.items" :key="item.id" class="mb-1">
                  {{ item.product?.nama }} ({{ item.quantity }} {{ item.product?.satuan }})
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                Rp {{ formatCurrency(purchase.total_amount) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="viewPurchase(purchase)" class="text-blue-600 hover:text-blue-900 mr-3">
                Detail
              </button>
              <button @click="editPurchase(purchase)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                Edit
              </button>
              <button @click="deletePurchase(purchase)" class="text-red-600 hover:text-red-900">
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
            {{ isEditing ? 'Edit Pembelian' : 'Tambah Pembelian Baru' }}
          </h3>
          
          <form @submit.prevent="savePurchase">
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <label class="form-label">Supplier</label>
                <select v-model="form.supplier_id" @change="filterProductsBySupplier" class="form-input" required>
                  <option value="">Pilih Supplier</option>
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.nama }}
                  </option>
                </select>
              </div>
              <div>
                <label class="form-label">Tanggal Pembelian</label>
                <input v-model="form.purchase_date" type="date" class="form-input" required>
              </div>
            </div>

            <!-- Items Section -->
            <div class="mb-6">
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">Items Pembelian</h4>
                <button type="button" @click="addItem" class="btn-success" :disabled="!form.supplier_id">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Tambah Item
                </button>
              </div>

              <div v-if="!form.supplier_id" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      Pilih supplier terlebih dahulu untuk menambahkan item pembelian.
                    </p>
                  </div>
                </div>
              </div>

              <div v-for="(item, index) in form.items" :key="index" class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <label class="form-label">Produk</label>
                    <select v-model="item.product_id" @change="updateItemPrice(index)" class="form-input" required>
                      <option value="">Pilih Produk</option>
                      <option v-for="product in filteredProducts" :key="product.id" :value="product.id">
                        {{ product.nama }}
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
              <button type="submit" class="btn-primary" :disabled="submitting || form.items.length === 0 || !form.supplier_id">
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
          <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pembelian</h3>
          
          <div v-if="selectedPurchase" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <strong>Supplier:</strong> {{ selectedPurchase.supplier?.nama }}
              </div>
              <div>
                <strong>Tanggal:</strong> {{ formatDate(selectedPurchase.purchase_date) }}
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
                  <tr v-for="item in selectedPurchase.items" :key="item.id">
                    <td class="px-4 py-2">{{ item.product?.nama }}</td>
                    <td class="px-4 py-2">{{ item.quantity }}</td>
                    <td class="px-4 py-2">Rp {{ formatCurrency(item.unit_price) }}</td>
                    <td class="px-4 py-2">Rp {{ formatCurrency(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="text-right font-bold">
              Total: Rp {{ formatCurrency(selectedPurchase.total_amount) }}
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
import { ref, onMounted, computed } from 'vue'
import { purchasesAPI, suppliersAPI, productsAPI } from '../services/api'

export default {
  name: 'Purchases',
  setup() {
    const purchases = ref([])
    const suppliers = ref([])
    const products = ref([])
    const loading = ref(false)
    const showModal = ref(false)
    const showDetailModal = ref(false)
    const isEditing = ref(false)
    const submitting = ref(false)
    const editingId = ref(null)
    const selectedPurchase = ref(null)
    
    const form = ref({
      supplier_id: '',
      purchase_date: '',
      items: []
    })

    const filteredProducts = computed(() => {
      if (!form.value.supplier_id) return []
      return products.value.filter(product => product.supplier_id == form.value.supplier_id)
    })

    const loadPurchases = async () => {
      loading.value = true
      try {
        const response = await purchasesAPI.getAll()
        purchases.value = response.data.data
      } catch (error) {
        console.error('Error loading purchases:', error)
        alert('Error loading purchases: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
      }
    }

    const loadSuppliers = async () => {
      try {
        const response = await suppliersAPI.getAll()
        suppliers.value = response.data.data
      } catch (error) {
        console.error('Error loading suppliers:', error)
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
      selectedPurchase.value = null
    }

    const resetForm = () => {
      form.value = {
        supplier_id: '',
        purchase_date: '',
        items: []
      }
      editingId.value = null
    }

    const filterProductsBySupplier = () => {
      // Clear items when supplier changes
      form.value.items = []
    }

    const addItem = () => {
      if (!form.value.supplier_id) {
        alert('Pilih supplier terlebih dahulu!')
        return
      }
      
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
        item.unit_price = product.harga_beli
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

    const viewPurchase = (purchase) => {
      selectedPurchase.value = purchase
      showDetailModal.value = true
    }

    const editPurchase = (purchase) => {
      form.value = {
        supplier_id: purchase.supplier_id,
        purchase_date: purchase.purchase_date,
        items: purchase.items.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          unit_price: item.unit_price,
          subtotal: item.subtotal
        }))
      }
      editingId.value = purchase.id
      isEditing.value = true
      showModal.value = true
    }

    const savePurchase = async () => {
      // Validate that all products belong to selected supplier
      for (const item of form.value.items) {
        const product = products.value.find(p => p.id == item.product_id)
        if (product && product.supplier_id != form.value.supplier_id) {
          alert(`Produk ${product.nama} tidak dari supplier yang dipilih!`)
          return
        }
      }

      submitting.value = true
      try {
        if (isEditing.value) {
          await purchasesAPI.update(editingId.value, form.value)
        } else {
          await purchasesAPI.create(form.value)
        }
        closeModal()
        loadPurchases()
        loadProducts() // Refresh products to update stock
        alert(isEditing.value ? 'Pembelian berhasil diperbarui!' : 'Pembelian berhasil ditambahkan!')
      } catch (error) {
        console.error('Error saving purchase:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      } finally {
        submitting.value = false
      }
    }

    const deletePurchase = async (purchase) => {
      if (!confirm(`Apakah Anda yakin ingin menghapus pembelian dari "${purchase.supplier?.nama}"?`)) {
        return
      }

      try {
        await purchasesAPI.delete(purchase.id)
        loadPurchases()
        loadProducts() // Refresh products to update stock
        alert('Pembelian berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting purchase:', error)
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
      loadPurchases()
      loadSuppliers()
      loadProducts()
    })

    return {
      purchases,
      suppliers,
      products,
      filteredProducts,
      loading,
      showModal,
      showDetailModal,
      isEditing,
      submitting,
      selectedPurchase,
      form,
      openModal,
      closeModal,
      closeDetailModal,
      filterProductsBySupplier,
      addItem,
      removeItem,
      updateItemPrice,
      calculateSubtotal,
      calculateTotal,
      viewPurchase,
      editPurchase,
      savePurchase,
      deletePurchase,
      formatCurrency,
      formatDate
    }
  }
}
</script> 