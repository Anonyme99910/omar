import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isManager: (state) => state.user?.role === 'manager',
    isCashier: (state) => state.user?.role === 'cashier',
    userPermissions: (state) => state.user?.permissions || [],
    hasPermission: (state) => (permission) => {
      if (state.user?.role === 'admin') return true
      return state.user?.permissions?.includes(permission) || false
    },
    hasAnyPermission: (state) => {
      if (state.user?.role === 'admin') return true
      return (state.user?.permissions?.length || 0) > 0
    }
  },

  actions: {
    async login(credentials) {
      try {
        const response = await api.login(credentials)
        this.user = response.data.user
        this.token = response.data.token
        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('token', response.data.token)
        return response.data
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        await api.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('user')
        localStorage.removeItem('token')
      }
    },

    async fetchUser() {
      try {
        const response = await api.getProfile()
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(response.data))
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      }
    },
  },
})
