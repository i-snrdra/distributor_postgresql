import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Suppliers from '../views/Suppliers.vue'
import Products from '../views/Products.vue'
import Sales from '../views/Sales.vue'
import Purchases from '../views/Purchases.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/suppliers',
    name: 'Suppliers',
    component: Suppliers
  },
  {
    path: '/products',
    name: 'Products',
    component: Products
  },
  {
    path: '/sales',
    name: 'Sales',
    component: Sales
  },
  {
    path: '/purchases',
    name: 'Purchases',
    component: Purchases
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router 