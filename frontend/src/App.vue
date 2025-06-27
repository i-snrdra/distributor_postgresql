<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between">
          <div class="flex space-x-7">
            <!-- Website Logo -->
            <div>
              <router-link to="/" class="flex items-center py-4 px-2">
                <span class="font-semibold text-gray-500 text-lg">Sistem Distributor</span>
              </router-link>
            </div>
          </div>
          
          <!-- Primary Navbar items -->
          <div class="hidden md:flex items-center space-x-1">
            <router-link 
              to="/" 
              class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300"
              :class="{ 'text-blue-500 border-b-4 border-blue-500': $route.path === '/' }"
            >
              Dashboard
            </router-link>
            <router-link 
              to="/suppliers" 
              class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300"
              :class="{ 'text-blue-500 border-b-4 border-blue-500': $route.path.includes('/suppliers') }"
            >
              Supplier
            </router-link>
            <router-link 
              to="/products" 
              class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300"
              :class="{ 'text-blue-500 border-b-4 border-blue-500': $route.path.includes('/products') }"
            >
              Produk
            </router-link>
            <router-link 
              to="/sales" 
              class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300"
              :class="{ 'text-blue-500 border-b-4 border-blue-500': $route.path.includes('/sales') }"
            >
              Penjualan
            </router-link>
            <router-link 
              to="/purchases" 
              class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300"
              :class="{ 'text-blue-500 border-b-4 border-blue-500': $route.path.includes('/purchases') }"
            >
              Pembelian
            </router-link>
          </div>

          <!-- Mobile menu button -->
          <div class="md:hidden flex items-center">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="outline-none mobile-menu-button">
              <svg class="w-6 h-6 text-gray-500" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="!mobileMenuOpen" d="M4 6h16M4 12h16M4 18h16"></path>
                <path v-else d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="md:hidden">
        <router-link to="/" class="block py-2 px-4 text-sm hover:bg-gray-200">Dashboard</router-link>
        <router-link to="/suppliers" class="block py-2 px-4 text-sm hover:bg-gray-200">Supplier</router-link>
        <router-link to="/products" class="block py-2 px-4 text-sm hover:bg-gray-200">Produk</router-link>
        <router-link to="/sales" class="block py-2 px-4 text-sm hover:bg-gray-200">Penjualan</router-link>
        <router-link to="/purchases" class="block py-2 px-4 text-sm hover:bg-gray-200">Pembelian</router-link>
      </div>
    </nav>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view />
    </main>

    <!-- Loading overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'App',
  setup() {
    const mobileMenuOpen = ref(false)
    const loading = ref(false)

    return {
      mobileMenuOpen,
      loading
    }
  }
}
</script> 