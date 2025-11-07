<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">نظام إدارة متجر العطور</h1>
          <p class="text-gray-600">مرحباً بك، قم بتسجيل الدخول للمتابعة</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input"
              placeholder="admin@perfume.com"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="input"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary w-full py-3 text-lg"
          >
            <span v-if="!loading">تسجيل الدخول</span>
            <span v-else>جاري التحميل...</span>
          </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
          <p>البيانات الافتراضية:</p>
          <p class="mt-1">admin@perfume.com / password</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  try {
    await authStore.login(form.value)
    toast.success('تم تسجيل الدخول بنجاح')
    router.push('/')
  } catch (error) {
    toast.error(error.response?.data?.error || 'فشل تسجيل الدخول')
  } finally {
    loading.value = false
  }
}
</script>
