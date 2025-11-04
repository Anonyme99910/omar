import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const loading = ref(false)
  const isAuthenticated = ref(false)

  async function login(email, password) {
    loading.value = true
    try {
      const data = await api.login(email, password)
      user.value = data.user
      isAuthenticated.value = true
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      isAuthenticated.value = false
    }
  }

  async function checkAuth() {
    const token = localStorage.getItem('admin_token')
    if (!token) {
      isAuthenticated.value = false
      return false
    }

    try {
      const userData = await api.getUser()
      if (userData.is_admin) {
        user.value = userData
        isAuthenticated.value = true
        return true
      }
      throw new Error('Not admin')
    } catch (error) {
      localStorage.removeItem('admin_token')
      isAuthenticated.value = false
      return false
    }
  }

  return {
    user,
    loading,
    isAuthenticated,
    login,
    logout,
    checkAuth
  }
})
