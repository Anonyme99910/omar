import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue')
      },
      {
        path: 'clients',
        name: 'Clients',
        component: () => import('@/views/Customers.vue')
      },
      {
        path: 'clients/:id',
        name: 'ClientDetails',
        component: () => import('@/views/ClientDetails.vue')
      },
      {
        path: 'employees',
        name: 'Employees',
        component: () => import('@/views/Employees.vue')
      },
      {
        path: 'roles',
        name: 'Roles',
        component: () => import('@/views/Roles.vue')
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('@/views/Profile.vue')
      },
      {
        path: 'pos',
        name: 'POS',
        component: () => import('@/views/POS.vue')
      },
      {
        path: 'inventory',
        name: 'Inventory',
        component: () => import('@/views/Inventory.vue')
      },
      {
        path: 'invoices',
        name: 'Invoices',
        component: () => import('@/views/Invoices/InvoicesList.vue')
      },
      {
        path: 'invoices/:id',
        name: 'InvoiceDetail',
        component: () => import('@/views/Invoices/InvoiceDetail.vue')
      },
      {
        path: 'stock',
        name: 'Stock',
        component: () => import('@/views/Stock/StockList.vue')
      },
      {
        path: 'import-products',
        name: 'ImportProducts',
        component: () => import('@/views/Stock/ImportProducts.vue')
      },
      {
        path: 'sales-analysis',
        name: 'SalesAnalysis',
        component: () => import('@/views/SalesAnalysis.vue')
      },
      {
        path: 'expenses',
        name: 'Expenses',
        component: () => import('@/views/Expenses/ExpensesList.vue')
      }
    ]
  },
  {
    path: '/no-access',
    name: 'NoAccess',
    component: () => import('@/views/NoAccess.vue')
  }
]

const router = createRouter({
  history: createWebHistory('/parfumes/'),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    // Redirect to first allowed page instead of dashboard
    if (!authStore.isAdmin && !authStore.hasPermission('dashboard')) {
      const firstAllowedRoute = getFirstAllowedRoute(authStore)
      next(firstAllowedRoute || '/no-access')
    } else {
      next('/')
    }
  } else if (to.meta.requiresAuth && authStore.isAuthenticated) {
    // Check if user has any permissions (skip for admin and profile page)
    if (to.path !== '/profile' && to.path !== '/no-access' && !authStore.hasAnyPermission) {
      next('/no-access')
    } else if (to.path === '/' && !authStore.isAdmin && !authStore.hasPermission('dashboard')) {
      // If trying to access dashboard without permission, redirect to first allowed page
      const firstAllowedRoute = getFirstAllowedRoute(authStore)
      next(firstAllowedRoute || '/no-access')
    } else {
      next()
    }
  } else {
    next()
  }
})

// Helper function to get first allowed route
function getFirstAllowedRoute(authStore) {
  const routeMap = {
    'clients': '/clients',
    'employees': '/employees',
    'roles': '/roles',
    'pos': '/pos',
    'invoices': '/invoices',
    'sales-analysis': '/sales-analysis',
    'expenses': '/expenses',
    'stock': '/stock',
    'inventory': '/inventory'
  }
  
  for (const [permission, route] of Object.entries(routeMap)) {
    if (authStore.hasPermission(permission)) {
      return route
    }
  }
  
  return null
}

export default router
