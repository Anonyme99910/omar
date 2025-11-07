<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <div class="w-24 h-24 mx-auto rounded-full bg-red-100 flex items-center justify-center">
          <ShieldOff :size="48" class="text-red-600" />
        </div>
      </div>
      
      <h1 class="text-3xl font-bold text-gray-900 mb-4">لا توجد صلاحيات</h1>
      <p class="text-gray-600 mb-8">
        عذراً، لا تملك أي صلاحيات للوصول إلى النظام.<br />
        يرجى التواصل مع المدير لمنحك الصلاحيات المناسبة.
      </p>
      
      <div class="space-y-3">
        <button
          @click="handleLogout"
          class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2"
        >
          <LogOut :size="20" />
          تسجيل الخروج
        </button>
        
        <button
          @click="checkAgain"
          class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
        >
          التحقق مرة أخرى
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ShieldOff, LogOut } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const checkAgain = async () => {
  try {
    const res = await api.getProfile()
    authStore.user = res.data
    localStorage.setItem('user', JSON.stringify(res.data))
    
    if (res.data.role === 'admin' || (res.data.permissions && res.data.permissions.length > 0)) {
      router.push('/')
    }
  } catch (error) {
    console.error('Failed to check permissions:', error)
  }
}
</script>
