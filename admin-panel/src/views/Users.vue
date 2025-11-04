<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4 space-x-reverse">
          <router-link to="/dashboard" class="text-gray-600 hover:text-brown">
            ← العودة
          </router-link>
          <h1 class="text-2xl font-bold text-brown">👥 إدارة المستخدمين</h1>
        </div>
        <button @click="handleLogout" class="btn btn-secondary">
          تسجيل الخروج
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <input
            v-model="filters.search"
            @input="searchUsers"
            type="text"
            placeholder="بحث بالاسم أو البريد..."
            class="input"
          />
          <select v-model="filters.is_active" @change="loadUsers" class="input">
            <option value="">جميع المستخدمين</option>
            <option value="1">نشط</option>
            <option value="0">غير نشط</option>
          </select>
          <div class="text-gray-600 flex items-center">
            إجمالي: <span class="font-bold mr-2">{{ pagination.total || 0 }}</span> مستخدم
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-brown"></div>
        <p class="mt-4 text-gray-600">جاري التحميل...</p>
      </div>

      <!-- Users Table -->
      <div v-else class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العقارات</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-full bg-brown text-white flex items-center justify-center font-bold">
                        {{ user.full_name.charAt(0) }}
                      </div>
                    </div>
                    <div class="mr-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.full_name }}</div>
                      <div v-if="user.is_admin" class="text-xs text-purple-600">مسؤول</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.phone_number || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="badge bg-blue-100 text-blue-800">
                    {{ user.properties_count || 0 }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="user.is_active ? 'badge-active' : 'badge-inactive'" class="badge">
                    {{ user.is_active ? 'نشط' : 'غير نشط' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    v-if="!user.is_admin"
                    @click="toggleUserStatus(user)"
                    :class="user.is_active ? 'btn-danger' : 'btn-success'"
                    class="btn btn-sm"
                  >
                    {{ user.is_active ? 'تعطيل' : 'تفعيل' }}
                  </button>
                  <span v-else class="text-gray-400 text-xs">مسؤول</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="btn btn-secondary"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
          >
            السابق
          </button>
          <span class="text-sm text-gray-700">
            صفحة {{ pagination.current_page }} من {{ pagination.last_page }}
          </span>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="btn btn-secondary"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
          >
            التالي
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && users.length === 0" class="card text-center py-12">
        <div class="text-6xl mb-4">👥</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">لا يوجد مستخدمين</h3>
        <p class="text-gray-600">لم يتم العثور على أي مستخدمين</p>
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
const users = ref([])
const filters = ref({
  search: '',
  is_active: ''
})
const pagination = ref({})

let searchTimeout = null

onMounted(async () => {
  await loadUsers()
})

async function loadUsers(page = 1) {
  try {
    loading.value = true
    const params = {
      page,
      per_page: 20,
      ...filters.value
    }
    const response = await api.getUsers(params)
    users.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total
    }
  } catch (error) {
    console.error('Failed to load users:', error)
  } finally {
    loading.value = false
  }
}

function searchUsers() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadUsers()
  }, 500)
}

function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadUsers(page)
  }
}

async function toggleUserStatus(user) {
  if (!confirm(`هل تريد ${user.is_active ? 'تعطيل' : 'تفعيل'} هذا المستخدم؟`)) {
    return
  }

  try {
    await api.toggleUserStatus(user.id)
    user.is_active = !user.is_active
  } catch (error) {
    alert('فشل في تحديث حالة المستخدم')
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
