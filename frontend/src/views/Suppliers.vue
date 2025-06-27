<template>
  <div>
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Manajemen Supplier
        </h1>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button @click="openModal()" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Supplier
        </button>
      </div>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>
      
      <div v-else-if="suppliers.length === 0" class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada supplier</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan supplier baru</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nama
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Kontak
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Alamat
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Produk
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="supplier in suppliers" :key="supplier.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ supplier.nama }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ supplier.nomor_telepon }}</div>
              <div class="text-sm text-gray-500">{{ supplier.email }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">{{ supplier.alamat }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ supplier.products_count || 0 }} produk
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="editSupplier(supplier)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                Edit
              </button>
              <button @click="deleteSupplier(supplier)" class="text-red-600 hover:text-red-900">
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
            {{ isEditing ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
          </h3>
          
          <form @submit.prevent="saveSupplier">
            <div class="mb-4">
              <label class="form-label">Nama Perusahaan</label>
              <input v-model="form.nama" type="text" class="form-input" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label">Alamat</label>
              <textarea v-model="form.alamat" class="form-input" rows="3" required></textarea>
            </div>
            
            <div class="mb-4">
              <label class="form-label">Nomor Telepon</label>
              <input v-model="form.nomor_telepon" type="text" class="form-input" required>
            </div>
            
            <div class="mb-6">
              <label class="form-label">Email</label>
              <input v-model="form.email" type="email" class="form-input" required>
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
import { suppliersAPI } from '../services/api'

export default {
  name: 'Suppliers',
  setup() {
    const suppliers = ref([])
    const loading = ref(false)
    const showModal = ref(false)
    const isEditing = ref(false)
    const submitting = ref(false)
    const editingId = ref(null)
    
    const form = ref({
      nama: '',
      alamat: '',
      nomor_telepon: '',
      email: ''
    })

    const loadSuppliers = async () => {
      loading.value = true
      try {
        const response = await suppliersAPI.getAll()
        suppliers.value = response.data.data
      } catch (error) {
        console.error('Error loading suppliers:', error)
        alert('Error loading suppliers: ' + (error.response?.data?.message || error.message))
      } finally {
        loading.value = false
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
        alamat: '',
        nomor_telepon: '',
        email: ''
      }
      editingId.value = null
    }

    const editSupplier = (supplier) => {
      form.value = { ...supplier }
      editingId.value = supplier.id
      isEditing.value = true
      showModal.value = true
    }

    const saveSupplier = async () => {
      submitting.value = true
      try {
        if (isEditing.value) {
          await suppliersAPI.update(editingId.value, form.value)
        } else {
          await suppliersAPI.create(form.value)
        }
        closeModal()
        loadSuppliers()
        alert(isEditing.value ? 'Supplier berhasil diperbarui!' : 'Supplier berhasil ditambahkan!')
      } catch (error) {
        console.error('Error saving supplier:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      } finally {
        submitting.value = false
      }
    }

    const deleteSupplier = async (supplier) => {
      if (!confirm(`Apakah Anda yakin ingin menghapus supplier "${supplier.nama}"?`)) {
        return
      }

      try {
        await suppliersAPI.delete(supplier.id)
        loadSuppliers()
        alert('Supplier berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting supplier:', error)
        alert('Error: ' + (error.response?.data?.message || error.message))
      }
    }

    onMounted(() => {
      loadSuppliers()
    })

    return {
      suppliers,
      loading,
      showModal,
      isEditing,
      submitting,
      form,
      openModal,
      closeModal,
      editSupplier,
      saveSupplier,
      deleteSupplier
    }
  }
}
</script> 