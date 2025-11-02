<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">الملف الشخصي</h2>
        <p class="text-gray-500 mt-1">إدارة معلوماتك الشخصية</p>
      </div>
    </div>

    <!-- Profile Card -->
    <div class="card">
      <div class="flex items-start gap-6 mb-6 pb-6 border-b">
        <!-- Avatar -->
        <div class="relative">
          <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
            <User :size="48" class="text-white" />
          </div>
          <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-4 border-white"></div>
        </div>

        <!-- User Info -->
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-gray-900">{{ user.name }}</h3>
          <p class="text-gray-500 mt-1">{{ user.email }}</p>
          <div class="flex items-center gap-2 mt-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
              {{ getRoleLabel(user.role) }}
            </span>
            <span v-if="user.is_active" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
              نشط
            </span>
          </div>
        </div>

        <!-- Edit Button -->
        <button
          @click="isEditing = true"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
        >
          <Edit2 :size="18" />
          تعديل
        </button>
      </div>

      <!-- Profile Details -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل</label>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <User :size="20" class="text-gray-400" />
              <span class="text-gray-900">{{ user.name }}</span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <Mail :size="20" class="text-gray-400" />
              <span class="text-gray-900">{{ user.email }}</span>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الدور الوظيفي</label>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <Briefcase :size="20" class="text-gray-400" />
              <span class="text-gray-900">{{ getRoleLabel(user.role) }}</span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانضمام</label>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <Calendar :size="20" class="text-gray-400" />
              <span class="text-gray-900">{{ formatDate(user.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      
    </div>

    <!-- Edit Modal -->
    <div v-if="isEditing" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeEditModal">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">تعديل الملف الشخصي</h3>
            <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
              <X :size="24" />
            </button>
          </div>

          <!-- Edit Form -->
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
              <input
                v-model="editForm.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="أدخل الاسم الكامل"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
              <input
                v-model="editForm.email"
                type="email"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="أدخل البريد الإلكتروني"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة (اختياري)</label>
              <input
                v-model="editForm.password"
                type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="اتركه فارغاً إذا لم ترغب في التغيير"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
              <input
                v-model="editForm.password_confirmation"
                type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="أعد إدخال كلمة المرور"
              />
            </div>

            <!-- Modal Actions -->
            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
              >
                حفظ التغييرات
              </button>
              <button
                type="button"
                @click="closeEditModal"
                class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
              >
                إلغاء
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { User, Mail, Briefcase, Calendar, Edit2, LogOut, X } from 'lucide-vue-next'
import api from '@/services/api'
import { toLatinNumbers } from '@/utils/numbers'
import { useToast } from 'vue-toastification'

const router = useRouter()
const toast = useToast()

const user = ref({})
const isEditing = ref(false)
const editForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const getRoleLabel = (role) => {
  const labels = {
    'admin': 'مدير النظام',
    'manager': 'مدير فرع',
    'cashier': 'كاشير',
    'inventory': 'مسؤول مخزن'
  }
  return labels[role] || role
}

const formatDate = (date) => {
  if (!date) return '-'
  const formatted = new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
  return toLatinNumbers(formatted)
}

const fetchProfile = async () => {
  try {
    const res = await api.getProfile()
    user.value = res.data
  } catch (error) {
    console.error('Failed to load profile:', error)
    toast.error('فشل تحميل الملف الشخصي')
  }
}

const closeEditModal = () => {
  isEditing.value = false
  editForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  }
}

const saveProfile = async () => {
  if (editForm.value.password && editForm.value.password !== editForm.value.password_confirmation) {
    toast.error('كلمة المرور غير متطابقة')
    return
  }

  try {
    const data = {
      name: editForm.value.name,
      email: editForm.value.email
    }

    if (editForm.value.password) {
      data.password = editForm.value.password
      data.password_confirmation = editForm.value.password_confirmation
    }

    const res = await api.updateProfile(data)
    user.value = res.data
    toast.success('تم تحديث الملف الشخصي بنجاح')
    closeEditModal()
  } catch (error) {
    console.error('Failed to update profile:', error)
    toast.error('فشل تحديث الملف الشخصي')
  }
}

const handleLogout = async () => {
  if (!confirm('هل أنت متأكد من تسجيل الخروج؟')) return

  try {
    await api.logout()
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    toast.success('تم تسجيل الخروج بنجاح')
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    // Force logout even if API fails
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

onMounted(() => {
  fetchProfile()
  
  // Pre-fill edit form when opening modal
  if (user.value.id) {
    editForm.value.name = user.value.name
    editForm.value.email = user.value.email
  }
})

// Watch for user changes to update edit form
watch(() => user.value, (newUser) => {
  if (newUser.id) {
    editForm.value.name = newUser.name
    editForm.value.email = newUser.email
  }
}, { deep: true })
</script>
