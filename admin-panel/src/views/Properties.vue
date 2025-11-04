<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4 space-x-reverse">
          <router-link to="/dashboard" class="text-gray-600 hover:text-brown">
            ← العودة
          </router-link>
          <h1 class="text-2xl font-bold text-brown">🏠 إدارة العقارات</h1>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input
            v-model="filters.search"
            @input="searchProperties"
            type="text"
            placeholder="بحث بالعنوان أو الموقع..."
            class="input"
          />
          <select v-model="filters.status" @change="loadProperties" class="input">
            <option value="">جميع الحالات</option>
            <option value="pending">قيد الانتظار</option>
            <option value="approved">معتمد</option>
            <option value="rejected">مرفوض</option>
          </select>
          <select v-model="filters.category" @change="loadProperties" class="input">
            <option value="">جميع الفئات</option>
            <option value="apartment">شقة</option>
            <option value="villa">فيلا</option>
            <option value="land">أرض</option>
            <option value="commercial">تجاري</option>
          </select>
          <div class="text-gray-600 flex items-center">
            إجمالي: <span class="font-bold mr-2">{{ pagination.total || 0 }}</span> عقار
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-brown"></div>
        <p class="mt-4 text-gray-600">جاري التحميل...</p>
      </div>

      <!-- Properties Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="property in properties" :key="property.id" class="card hover:shadow-lg transition-shadow">
          <!-- Image -->
          <div class="relative h-48 bg-gray-200 rounded-t-lg overflow-hidden">
            <img
              v-if="property.images && property.images.length"
              :src="getImageUrl(property.images[0])"
              :alt="property.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-5xl">
              🏠
            </div>
            <div class="absolute top-2 right-2">
              <span :class="{
                'badge-pending': property.status === 'pending',
                'badge-approved': property.status === 'approved',
                'badge-rejected': property.status === 'rejected'
              }" class="badge">
                {{ getStatusText(property.status) }}
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-1">
              {{ property.title }}
            </h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
              {{ property.description }}
            </p>
            
            <div class="space-y-2 mb-4">
              <div class="flex items-center text-sm text-gray-600">
                <span class="mr-2">📍</span>
                {{ property.location }}
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <span class="mr-2">💰</span>
                {{ property.price }} {{ property.price_unit }}
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <span class="mr-2">👤</span>
                {{ property.owner?.full_name || 'غير معروف' }}
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                v-if="property.status === 'pending'"
                @click="updateStatus(property, 'approved')"
                class="btn btn-success flex-1"
              >
                ✅ قبول
              </button>
              <button
                v-if="property.status === 'pending'"
                @click="updateStatus(property, 'rejected')"
                class="btn btn-danger flex-1"
              >
                ❌ رفض
              </button>
              <button
                @click="deleteProperty(property)"
                class="btn btn-danger"
              >
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && pagination.last_page > 1" class="mt-6 flex items-center justify-center gap-4">
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

      <!-- Empty State -->
      <div v-if="!loading && properties.length === 0" class="card text-center py-12">
        <div class="text-6xl mb-4">🏠</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">لا يوجد عقارات</h3>
        <p class="text-gray-600">لم يتم العثور على أي عقارات</p>
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
const properties = ref([])
const filters = ref({
  search: '',
  status: 'pending',
  category: ''
})
const pagination = ref({})

let searchTimeout = null

onMounted(async () => {
  await loadProperties()
})

async function loadProperties(page = 1) {
  try {
    loading.value = true
    const params = {
      page,
      per_page: 12,
      ...filters.value
    }
    const response = await api.getProperties(params)
    properties.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total
    }
  } catch (error) {
    console.error('Failed to load properties:', error)
  } finally {
    loading.value = false
  }
}

function searchProperties() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadProperties()
  }, 500)
}

function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadProperties(page)
  }
}

async function updateStatus(property, status) {
  const statusText = status === 'approved' ? 'قبول' : 'رفض'
  if (!confirm(`هل تريد ${statusText} هذا العقار؟`)) {
    return
  }

  try {
    await api.updatePropertyStatus(property.id, status)
    property.status = status
  } catch (error) {
    alert('فشل في تحديث حالة العقار')
  }
}

async function deleteProperty(property) {
  if (!confirm('هل تريد حذف هذا العقار نهائياً؟')) {
    return
  }

  try {
    await api.deleteProperty(property.id)
    properties.value = properties.value.filter(p => p.id !== property.id)
  } catch (error) {
    alert('فشل في حذف العقار')
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

function getImageUrl(image) {
  if (image.startsWith('http')) {
    return image
  }
  return `http://localhost/parfumes/backend/public/storage/${image}`
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
