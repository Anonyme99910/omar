<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-brown">📊 لوحة التحكم</h1>
        <button @click="handleLogout" class="btn btn-secondary">
          تسجيل الخروج
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-brown"></div>
        <p class="mt-4 text-gray-600">جاري التحميل...</p>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm">إجمالي المستخدمين</p>
                <p class="text-3xl font-bold mt-2">{{ stats.total_users || 0 }}</p>
              </div>
              <div class="text-5xl opacity-50">👥</div>
            </div>
          </div>

          <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm">إجمالي العقارات</p>
                <p class="text-3xl font-bold mt-2">{{ stats.total_properties || 0 }}</p>
              </div>
              <div class="text-5xl opacity-50">🏠</div>
            </div>
          </div>

          <div class="card bg-gradient-to-br from-yellow-500 to-yellow-600 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-yellow-100 text-sm">قيد الانتظار</p>
                <p class="text-3xl font-bold mt-2">{{ stats.pending_properties || 0 }}</p>
              </div>
              <div class="text-5xl opacity-50">⏳</div>
            </div>
          </div>

          <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm">معتمدة</p>
                <p class="text-3xl font-bold mt-2">{{ stats.approved_properties || 0 }}</p>
              </div>
              <div class="text-5xl opacity-50">✅</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <router-link to="/users" class="card hover:shadow-lg transition-shadow cursor-pointer">
            <div class="text-center py-6">
              <div class="text-5xl mb-4">👥</div>
              <h3 class="text-xl font-bold text-gray-800">إدارة المستخدمين</h3>
              <p class="text-gray-600 mt-2">عرض وإدارة جميع المستخدمين</p>
            </div>
          </router-link>

          <router-link to="/properties" class="card hover:shadow-lg transition-shadow cursor-pointer">
            <div class="text-center py-6">
              <div class="text-5xl mb-4">🏠</div>
              <h3 class="text-xl font-bold text-gray-800">إدارة العقارات</h3>
              <p class="text-gray-600 mt-2">الموافقة على العقارات وإدارتها</p>
            </div>
          </router-link>

          <div class="card bg-brown text-white">
            <div class="text-center py-6">
              <div class="text-5xl mb-4">📊</div>
              <h3 class="text-xl font-bold">الإحصائيات</h3>
              <p class="opacity-90 mt-2">تقارير وتحليلات مفصلة</p>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Users -->
          <div class="card">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
              <span class="text-2xl mr-2">👥</span>
              أحدث المستخدمين
            </h3>
            <div v-if="stats.recent_users && stats.recent_users.length" class="space-y-3">
              <div v-for="user in stats.recent_users" :key="user.id" 
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                  <p class="font-medium text-gray-800">{{ user.full_name }}</p>
                  <p class="text-sm text-gray-600">{{ user.email }}</p>
                </div>
                <span :class="user.is_active ? 'badge-active' : 'badge-inactive'" class="badge">
                  {{ user.is_active ? 'نشط' : 'غير نشط' }}
                </span>
              </div>
            </div>
            <p v-else class="text-gray-500 text-center py-4">لا يوجد مستخدمين</p>
          </div>

          <!-- Recent Properties -->
          <div class="card">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
              <span class="text-2xl mr-2">🏠</span>
              أحدث العقارات
            </h3>
            <div v-if="stats.recent_properties && stats.recent_properties.length" class="space-y-3">
              <div v-for="property in stats.recent_properties" :key="property.id" 
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                  <p class="font-medium text-gray-800">{{ property.title }}</p>
                  <p class="text-sm text-gray-600">{{ property.location }}</p>
                </div>
                <span :class="{
                  'badge-pending': property.status === 'pending',
                  'badge-approved': property.status === 'approved',
                  'badge-rejected': property.status === 'rejected'
                }" class="badge">
                  {{ getStatusText(property.status) }}
                </span>
              </div>
            </div>
            <p v-else class="text-gray-500 text-center py-4">لا يوجد عقارات</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const stats = ref({})

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  try {
    loading.value = true
    stats.value = await api.getDashboard()
  } catch (error) {
    console.error('Failed to load dashboard:', error)
  } finally {
    loading.value = false
  }
}

function getStatusText(status) {
  const statusMap = {
    pending: 'قيد الانتظار',
    approved: 'معتمد',
    rejected: 'مرفوض'
  }
  return statusMap[status] || status
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
