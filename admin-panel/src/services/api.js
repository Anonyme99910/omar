import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

class AdminAPI {
  constructor() {
    this.client = axios.create({
      baseURL: API_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    // Add token to requests
    this.client.interceptors.request.use(config => {
      const token = localStorage.getItem('admin_token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })

    // Handle 401 errors
    this.client.interceptors.response.use(
      response => response,
      error => {
        if (error.response?.status === 401) {
          localStorage.removeItem('admin_token')
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    )
  }

  // Auth
  async login(email, password) {
    const { data } = await this.client.post('/login', { email, password })
    if (data.user?.is_admin) {
      localStorage.setItem('admin_token', data.token)
      return data
    }
    throw new Error('غير مصرح لك بالدخول')
  }

  async logout() {
    await this.client.post('/logout')
    localStorage.removeItem('admin_token')
  }

  async getUser() {
    const { data } = await this.client.get('/user')
    return data
  }

  // Dashboard
  async getDashboard() {
    const { data } = await this.client.get('/admin/dashboard')
    return data
  }

  async getStatsByCategory() {
    const { data } = await this.client.get('/admin/statistics/category')
    return data
  }

  async getStatsByStatus() {
    const { data } = await this.client.get('/admin/statistics/status')
    return data
  }

  // Users
  async getUsers(params = {}) {
    const { data } = await this.client.get('/admin/users', { params })
    return data
  }

  async toggleUserStatus(userId) {
    const { data } = await this.client.put(`/admin/users/${userId}/toggle-status`)
    return data
  }

  // Properties
  async getProperties(params = {}) {
    const { data } = await this.client.get('/admin/properties', { params })
    return data
  }

  async updatePropertyStatus(propertyId, status) {
    const { data } = await this.client.put(`/admin/properties/${propertyId}/status`, { status })
    return data
  }

  async deleteProperty(propertyId) {
    const { data } = await this.client.delete(`/admin/properties/${propertyId}`)
    return data
  }
}

export const api = new AdminAPI()
