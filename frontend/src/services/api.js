import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'https://gt-academy.com/parfumes/backend/public/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true
})

// Add token to requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default {
  // Auth
  login: (credentials) => api.post('/login', credentials),
  register: (data) => api.post('/register', data),
  logout: () => api.post('/logout'),
  me: () => api.get('/me'),

  // Categories
  getCategories: () => api.get('/categories'),
  createCategory: (data) => api.post('/categories', data),
  updateCategory: (id, data) => api.put(`/categories/${id}`, data),
  deleteCategory: (id) => api.delete(`/categories/${id}`),

  // Brands
  getBrands: () => api.get('/brands'),
  createBrand: (data) => api.post('/brands', data),
  updateBrand: (id, data) => api.put(`/brands/${id}`, data),
  deleteBrand: (id) => api.delete(`/brands/${id}`),

  // Products
  getProducts: (params) => api.get('/products', { params }),
  getProduct: (id) => api.get(`/products/${id}`),
  createProduct: (data) => api.post('/products', data, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  updateProduct: (id, data) => api.post(`/products/${id}`, data, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  deleteProduct: (id) => api.delete(`/products/${id}`),
  searchByBarcode: (barcode) => api.get(`/products/barcode/${barcode}`),
  getLowStockProducts: () => api.get('/products/low-stock/list'),
  adjustStock: (id, data) => api.post(`/products/${id}/adjust-stock`, data),
  
  // Product Import
  importProductsExcel: (formData) => api.post('/products/import/excel', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  importProductsSQL: (data) => api.post('/products/import/sql', data),
  downloadProductTemplate: () => api.get('/products/import/template', { responseType: 'blob' }),
  getSQLTemplate: () => api.get('/products/import/sql-template'),
  getImportGuide: () => api.get('/products/import/guide'),

  // Customers
  getCustomers: (params) => api.get('/customers', { params }),
  getCustomer: (id) => api.get(`/customers/${id}`),
  getCustomerHistory: (id, params) => api.get(`/customers/${id}/history`, { params }),
  createCustomer: (data) => api.post('/customers', data),
  updateCustomer: (id, data) => api.put(`/customers/${id}`, data),
  deleteCustomer: (id) => api.delete(`/customers/${id}`),

  // Sales
  getSales: (params) => api.get('/sales', { params }),
  getSale: (id) => api.get(`/sales/${id}`),
  createSale: (data) => api.post('/sales', data),
  cancelSale: (id) => api.post(`/sales/${id}/cancel`),
  getTodaySales: () => api.get('/sales/today/summary'),
  downloadInvoicePdf: async (id) => {
    try {
      const token = localStorage.getItem('token')
      if (!token) {
        throw new Error('No authentication token found')
      }

      const response = await axios.get(
        `http://localhost/parfumes/backend/public/api/sales/${id}/pdf`,
        {
          responseType: 'blob',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/pdf'
          }
        }
      )
      
      // Check if response is actually a blob
      if (response.data instanceof Blob) {
        const blob = new Blob([response.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `invoice-${id}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
      } else {
        throw new Error('Invalid response format')
      }
    } catch (error) {
      console.error('PDF download error:', error)
      if (error.response) {
        console.error('Response data:', error.response.data)
        console.error('Response status:', error.response.status)
      }
      throw error
    }
  },
  getWhatsAppMessage: (id) => api.get(`/sales/${id}/whatsapp`),
  voidSale: (id) => api.post(`/sales/${id}/void`),

  // Payments
  getPayments: (saleId) => api.get(`/sales/${saleId}/payments`),
  recordPayment: (saleId, data) => api.post(`/sales/${saleId}/payments`, data),

  // Stock Management
  getStock: (params) => api.get('/stock', { params }),
  getStockMovements: (params) => api.get('/stock/movements', { params }),
  adjustStockBulk: (data) => api.post('/stock/adjust', data), // ✅ Renamed to avoid conflict
  getLowStock: () => api.get('/stock/low-stock'),

  // Damaged Products
  getDamagedProducts: () => api.get('/damaged-products'),
  createDamagedProduct: (data) => api.post('/damaged-products', data),
  getDamagedStats: () => api.get('/damaged-products/stats'),
  deleteDamagedProduct: (id) => api.delete(`/damaged-products/${id}`),

  // Reports
  getDashboard: () => api.get('/reports/dashboard'),
  getSalesReport: (params) => api.get('/reports/sales', { params }),
  getProductReport: (params) => api.get('/reports/products', { params }),
  getInventoryReport: () => api.get('/reports/inventory'),
  getProfitReport: (params) => api.get('/reports/profit', { params }),

  // Employees
  getEmployees: (params) => api.get('/employees', { params }),
  getEmployee: (id) => api.get(`/employees/${id}`),
  createEmployee: (data) => api.post('/employees', data),
  updateEmployee: (id, data) => api.put(`/employees/${id}`, data),
  deleteEmployee: (id) => api.delete(`/employees/${id}`),
  updateEmployeePermissions: (id, data) => api.put(`/employees/${id}/permissions`, data),

  // Roles
  getRoles: () => api.get('/roles'),

  // Profile
  getProfile: () => api.get('/profile'),
  updateProfile: (data) => api.put('/profile', data),

  // Packages
  getPackages: () => api.get('/packages'),
  getPackage: (id) => api.get(`/packages/${id}`),
  createPackage: (data) => {
    const formData = new FormData()
    Object.keys(data).forEach(key => {
      if (key === 'photo' && data[key]) {
        formData.append('photo', data[key])
      } else if (Array.isArray(data[key])) {
        data[key].forEach((item, index) => {
          formData.append(`${key}[${index}]`, item)
        })
      } else {
        formData.append(key, data[key])
      }
    })
    return api.post('/packages', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },
  updatePackage: (id, data) => {
    const formData = new FormData()
    Object.keys(data).forEach(key => {
      if (key === 'photo' && data[key]) {
        formData.append('photo', data[key])
      } else if (Array.isArray(data[key])) {
        data[key].forEach((item, index) => {
          formData.append(`${key}[${index}]`, item)
        })
      } else {
        formData.append(key, data[key])
      }
    })
    return api.post(`/packages/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },
  deletePackage: (id) => api.delete(`/packages/${id}`),

  // Expenses
  getExpenses: (params) => api.get('/expenses', { params }),
  getExpense: (id) => api.get(`/expenses/${id}`),
  createExpense: (data) => api.post('/expenses', data),
  updateExpense: (id, data) => api.put(`/expenses/${id}`, data),
  deleteExpense: (id) => api.delete(`/expenses/${id}`),
  getExpenseStatistics: (params) => api.get('/expenses/statistics', { params }),

  // Expense Types
  getExpenseTypes: () => api.get('/expense-types'),
  createExpenseType: (data) => api.post('/expense-types', data),
  updateExpenseType: (id, data) => api.put(`/expense-types/${id}`, data),
  deleteExpenseType: (id) => api.delete(`/expense-types/${id}`),
}
