<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile Menu Button (open) -->
    <button
      v-if="!mobileMenuOpen"
      @click="mobileMenuOpen = true"
      class="lg:hidden fixed top-4 right-4 z-50 p-3 rounded-xl bg-primary-600 text-white shadow-lg active:scale-95"
      aria-label="open menu"
    >
      <Menu :size="26" />
    </button>

    <!-- Overlay -->
    <div
      v-if="mobileMenuOpen"
      @click="mobileMenuOpen = false"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed right-0 top-0 h-screen w-64 bg-white border-l border-gray-200 shadow-lg z-40 transition-transform duration-300',
        mobileMenuOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'
      ]"
    >
      <div class="p-6 flex items-start justify-between gap-3">
        <div>
          <h1 class="text-2xl font-bold text-primary-600 leading-tight">متجر العطور</h1>
          <p class="text-sm text-gray-500 mt-0.5">نظام الإدارة</p>
        </div>
        <!-- Close button inside sidebar (mobile) -->
        <button
          @click="mobileMenuOpen = false"
          class="lg:hidden p-2 rounded-lg bg-primary-600 text-white shadow"
          aria-label="close menu"
        >
          <X :size="20" />
        </button>
      </div>

      <nav class="px-4 space-y-1 pb-32">
        <template v-for="item in menuItems" :key="item.path">
          <router-link
            :to="item.path"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all hover:bg-primary-50 hover:text-primary-600"
            :class="{ 'bg-primary-50 text-primary-600': $route.path === item.path }"
          >
            <component :is="item.icon" :size="20" />
            <span class="font-medium">{{ item.name }}</span>
          </router-link>
          
          <!-- Sub-menu for Dashboard -->
          <div v-if="item.path === '/' && item.children" class="mr-6 mt-1 space-y-1 border-r pr-3 border-gray-100">
            <router-link
              v-for="child in item.children"
              :key="child.path"
              :to="child.path"
              class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-primary-50 hover:text-primary-600 text-sm"
              :class="{ 'bg-primary-50 text-primary-600': $route.path === child.path }"
            >
              <component :is="child.icon" :size="18" />
              <span class="font-medium">{{ child.name }}</span>
            </router-link>
          </div>
        </template>
      </nav>

      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white space-y-2">
        <!-- User Profile Button -->
        <router-link
          to="/profile"
          class="flex items-center gap-3 px-4 py-3 w-full rounded-lg transition-all hover:bg-blue-50 hover:text-blue-600 text-gray-700"
          :class="{ 'bg-blue-50 text-blue-600': $route.path === '/profile' }"
        >
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
            <User :size="16" class="text-white" />
          </div>
          <div class="flex-1 text-right">
            <div class="font-medium text-sm">{{ currentUser?.name || 'Admin' }}</div>
            <div class="text-xs text-gray-500">{{ currentUser?.role || 'admin' }}</div>
          </div>
        </router-link>
        
        <!-- Logout Button -->
        <button
          @click="handleLogout"
          class="flex items-center gap-3 px-4 py-3 w-full rounded-lg transition-all hover:bg-red-50 hover:text-red-600 text-gray-700"
        >
          <LogOut :size="20" />
          <span class="font-medium">تسجيل الخروج</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:mr-64 min-h-screen">
      <!-- Header -->
      <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
        <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
          <div class="flex-1 lg:flex-none pr-16 lg:pr-0">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">{{ currentPageTitle }}</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 hidden sm:block">{{ currentDate }}</p>
          </div>
          <div class="flex items-center gap-2 sm:gap-4">
            <div class="text-left">
              <p class="text-sm font-medium text-gray-900">{{ user?.name }}</p>
              <p class="text-xs text-gray-500">{{ user?.role }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
              <User :size="20" class="text-primary-600" />
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <div class="p-4 sm:p-6 lg:p-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { toLatinNumbers } from '@/utils/numbers'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useInactivityTimer } from '@/composables/useInactivityTimer'
import {
  LayoutDashboard,
  Package,
  ShoppingCart,
  Users,
  FileText,
  BarChart3,
  Tags,
  Boxes,
  LogOut,
  User,
  CreditCard,
  Menu,
  X,
  TrendingUp,
  Receipt
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// Initialize inactivity timer
const { timeUntilLogout, showWarning } = useInactivityTimer()

const user = computed(() => authStore.user)
const currentUser = computed(() => authStore.user)
const currentDate = ref('')
const mobileMenuOpen = ref(false)

const allMenuItems = [
  { 
    name: 'لوحة التحكم', 
    path: '/', 
    icon: LayoutDashboard,
    permission: 'dashboard',
    children: [
      { name: 'العملاء', path: '/clients', icon: Users, permission: 'clients' },
      { name: 'الموظفون', path: '/employees', icon: Users, permission: 'employees' },
      { name: 'الأدوار والصلاحيات', path: '/roles', icon: Tags, permission: 'roles' },
    ]
  },
  { name: 'نقطة البيع', path: '/pos', icon: CreditCard, permission: 'pos' },
  { name: 'الفواتير', path: '/invoices', icon: FileText, permission: 'invoices' },
  { name: 'تحليل المبيعات', path: '/sales-analysis', icon: TrendingUp, permission: 'sales-analysis' },
  { name: 'المصروفات', path: '/expenses', icon: Receipt, permission: 'expenses' },
  { name: 'المخزون', path: '/stock', icon: Package, permission: 'stock' },
  { name: 'المنتجات التالفة', path: '/inventory', icon: Boxes, permission: 'inventory' },
]

const menuItems = computed(() => {
  // Admin sees everything
  if (authStore.isAdmin) return allMenuItems
  
  // If no user or no permissions loaded, return empty
  if (!authStore.user || !authStore.user.permissions) return []
  
  // Filter menu items based on permissions
  return allMenuItems
    .filter(item => authStore.hasPermission(item.permission))
    .map(item => {
      // Create a copy of the item
      const filteredItem = { ...item }
      
      // Filter children if they exist
      if (filteredItem.children) {
        filteredItem.children = filteredItem.children.filter(child => 
          authStore.hasPermission(child.permission)
        )
      }
      
      return filteredItem
    })
})

const currentPageTitle = computed(() => {
  // Check main menu items
  const item = menuItems.value.find(i => i.path === route.path)
  if (item) return item.name
  
  // Check children
  for (const menuItem of menuItems.value) {
    if (menuItem.children) {
      const child = menuItem.children.find(c => c.path === route.path)
      if (child) return child.name
    }
  }
  
  return '\u0644\u0648\u062d\u0629 \u0627\u0644\u062a\u062d\u0643\u0645'
})

const handleLogout = async () => {
  await authStore.logout()
  window.location.href = '/parfumes/login'
}

onMounted(async () => {
  // Fetch fresh user data to ensure permissions are loaded
  if (authStore.isAuthenticated && !authStore.isAdmin) {
    await authStore.fetchUser()
  }
  
  const now = new Date()
  const arDate = now.toLocaleDateString('ar-EG', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
  currentDate.value = toLatinNumbers(arDate)
})
</script>
