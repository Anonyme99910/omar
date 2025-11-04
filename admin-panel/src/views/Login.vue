<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brown to-brown-light">
    <div class="card max-w-md w-full mx-4">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-brown mb-2">🌸 Parfumes</h1>
        <p class="text-gray-600">لوحة التحكم الإدارية</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
            البريد الإلكتروني
          </label>
          <input
            v-model="email"
            type="email"
            required
            class="input"
            placeholder="admin@parfumes.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
            كلمة المرور
          </label>
          <input
            v-model="password"
            type="password"
            required
            class="input"
            placeholder="••••••••"
          />
        </div>

        <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm text-right">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="btn btn-primary w-full"
        >
          <span v-if="loading">جاري تسجيل الدخول...</span>
          <span v-else>تسجيل الدخول</span>
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500">
        <p>مخصص للمسؤولين فقط</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true

  const result = await authStore.login(email.value, password.value)

  if (result.success) {
    router.push('/dashboard')
  } else {
    error.value = result.error || 'فشل تسجيل الدخول'
  }

  loading.value = false
}
</script>
