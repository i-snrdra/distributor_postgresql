<template>
  <div>
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Manajemen Produk
        </h1>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button @click="openModal()" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Produk
        </button>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>
      
      <div v-else-if="products.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada produk</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan produk baru</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Produk
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Supplier
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Stok
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Harga
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ product.nama }}</div>
              <div class="text-sm text-gray-500">{{ product.satuan }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ product.supplier?.nama }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStockBadgeClass(product.stok)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ product.stok }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                <div>Beli: Rp {{ formatCurrency(product.harga_beli) }}</div>
                <div>Jual: Rp {{ formatCurrency(product.harga_jual) }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="editProduct(product)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                Edit
              </button>
              <button @click="deleteProduct(product)" class="text-red-600 hover:text-red-900">
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}
          </h3>
          
          <form @submit.prevent="saveProduct">
            <div class="mb-4">
              <label class="form-label">Nama Produk</label>
              <input v-model="form.nama" type="text" class="form-input" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label">Supplier</label>
              <select v-model="form.supplier_id" class="form-input" required>
                <option value="">Pilih Supplier</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                  {{ supplier.nama }}
                </option>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="form-label">Stok</label>
                <input v-model.number="form.stok" type="number" min="0" class="form-input" required>
              </div>
              <div>
                <label class="form-label">Satuan</label>
                <input v-model="form.satuan" type="text" class="form-input" placeholder="dus, karton, kg" required>
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <label class="form-label">Harga Beli</label>
                <input v-model.number="form.harga_beli" type="number" min="0" class="form-input" required>
              </div>
              <div>
                <label class="form-label">Harga Jual</label>
                <input v-model.number="form.harga_jual" type="number" min="0" class="form-input" required>
              </div>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button type="button" @click="closeModal" class="btn-secondary">
                Batal
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting">
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { productsAPI, suppliersAPI } from '../services/api'

export default {
  name: 'Products',
  setup() {
    const products = ref([])
    const suppliers = ref([])
    const loading = ref(false)
    const showModal = ref(false)
    const isEditing = ref(false)
    const submitting = ref(false)
    const editingId = ref(null)
    
    const form = ref({
      nama: '',
      supplier_id: '',
      stok: 0,
      satuan: '',
      harga_beli: 0,
      harga_jual: 0
    })

    const loadProducts = async () => {
      loading.value = true
      try {
        const response = await productsAPI.getAll()
        products.value = response.data.data
      } catch (error) {
        console.error('Error loading products:', error)
        alert('Error loading products: ' + (error.response?.data?.message || error.message))
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

    const openModal = () => {
      showModal.value = true
      isEditing.value = false
      resetForm()
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const resetForm = () => {
      form.value = {
        nama: '',
        supplier_id: '',
        stok: 0,
        satuan: '',
        harga_beli: 0,
        harga_jual: 0
      }
      editingId.value = null
    }

    const editProduct = (product) => {
      form.value = { ...product }
      editingId.value = product.id
      isEditing.value = true
      showModal.value = true
    }

    const saveProduct = async () => {
      if (form.value.harga_jual <= form.value.harga_beli) {
        alert('Harga jual harus lebih besar dari harga beli!')
        return
      }

      submitting.value = true
      try {
        if (isEditing.value) {
          await productsAPI.update(editingId.value, form.value)
        } else {
          await productsAPI.create(form.value)
        }
        closeModal()
        loadProducts()
        alert(isEditing.value ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!')
      } catch (error) {
        console.error('Error saving product:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      } finally {
        submitting.value = false
      }
    }

    const deleteProduct = async (product) => {
      if (!confirm(`Apakah Anda yakin ingin menghapus produk "${product.nama}"?`)) {
        return
      }

      try {
        await productsAPI.delete(product.id)
        loadProducts()
        alert('Produk berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting product:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      }
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID').format(amount)
    }

    const getStockBadgeClass = (stock) => {
      if (stock === 0) return 'bg-red-100 text-red-800'
      if (stock < 10) return 'bg-yellow-100 text-yellow-800'
      return 'bg-green-100 text-green-800'
    }

    onMounted(() => {
      loadProducts()
      loadSuppliers()
    })

    return {
      products,
      suppliers,
      loading,
      showModal,
      isEditing,
      submitting,
      form,
      openModal,
      closeModal,
      editProduct,
      saveProduct,
      deleteProduct,
      formatCurrency,
      getStockBadgeClass
    }
  }
}
</script> 