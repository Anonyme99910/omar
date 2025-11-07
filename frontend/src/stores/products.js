import { defineStore } from 'pinia'
import api from '@/services/api'

export const useProductStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    brands: [],
    loading: false,
  }),

  actions: {
    async fetchProducts(params = {}) {
      this.loading = true
      try {
        const response = await api.getProducts(params)
        this.products = response.data
      } catch (error) {
        console.error('Error fetching products:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCategories() {
      try {
        const response = await api.getCategories()
        this.categories = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        throw error
      }
    },

    async fetchBrands() {
      try {
        const response = await api.getBrands()
        this.brands = response.data
      } catch (error) {
        console.error('Error fetching brands:', error)
        throw error
      }
    },

    async createProduct(data) {
      try {
        const response = await api.createProduct(data)
        this.products.push(response.data)
        return response.data
      } catch (error) {
        throw error
      }
    },

    async updateProduct(id, data) {
      try {
        const response = await api.updateProduct(id, data)
        const index = this.products.findIndex(p => p.id === id)
        if (index !== -1) {
          this.products[index] = response.data
        }
        return response.data
      } catch (error) {
        throw error
      }
    },

    async deleteProduct(id) {
      try {
        await api.deleteProduct(id)
        this.products = this.products.filter(p => p.id !== id)
      } catch (error) {
        throw error
      }
    },
  },
})
