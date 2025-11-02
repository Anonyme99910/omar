<template>
  <div class="p-4 sm:p-6 lg:p-8 space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">تحليل المبيعات</h1>
        <p class="text-gray-500 mt-1">تقارير شاملة ومفصلة لجميع المبيعات</p>
      </div>
      <button @click="exportToExcel" class="btn btn-primary flex items-center gap-2">
        <Download :size="20" />
        تصدير Excel
      </button>
    </div>

    <!-- Analysis Period Tabs -->
    <div class="card">
      <div class="flex flex-wrap gap-2 mb-6">
        <button
          v-for="period in periods"
          :key="period.value"
          @click="selectedPeriod = period.value; fetchSalesData()"
          :class="[
            'px-6 py-3 rounded-lg font-medium transition-all',
            selectedPeriod === period.value
              ? 'bg-primary-600 text-white shadow-lg'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ period.label }}
        </button>
      </div>

      <!-- Custom Date Range -->
      <div v-if="selectedPeriod === 'custom'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
        <div>
          <label class="block text-sm font-medium mb-2">من تاريخ</label>
          <input
            v-model="customDateRange.start"
            type="date"
            class="input w-full"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">إلى تاريخ</label>
          <input
            v-model="customDateRange.end"
            type="date"
            class="input w-full"
          />
        </div>
        <div class="sm:col-span-2">
          <button @click="fetchSalesData" class="btn btn-primary w-full">
            <Filter :size="18" />
            تطبيق الفلتر
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm">إجمالي المبيعات</p>
            <h3 class="text-3xl font-bold mt-2">{{ formatCurrencyLatin(stats.total_sales) }}</h3>
            <p class="text-blue-100 text-xs mt-1">{{ toLatinNumbers(stats.total_orders) }} فاتورة</p>
          </div>
          <div class="p-3 bg-white bg-opacity-20 rounded-xl">
            <DollarSign :size="32" />
          </div>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm">الفواتير المدفوعة</p>
            <h3 class="text-3xl font-bold mt-2">{{ formatCurrencyLatin(stats.paid_amount) }}</h3>
            <p class="text-green-100 text-xs mt-1">{{ toLatinNumbers(stats.paid_count) }} فاتورة</p>
          </div>
          <div class="p-3 bg-white bg-opacity-20 rounded-xl">
            <TrendingUp :size="32" />
          </div>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm">متوسط الفاتورة</p>
            <h3 class="text-3xl font-bold mt-2">{{ formatCurrencyLatin(stats.average_sale) }}</h3>
            <p class="text-purple-100 text-xs mt-1">لكل عملية بيع</p>
          </div>
          <div class="p-3 bg-white bg-opacity-20 rounded-xl">
            <BarChart3 :size="32" />
          </div>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm">المنتجات المباعة</p>
            <h3 class="text-3xl font-bold mt-2">{{ toLatinNumbers(stats.total_items) }}</h3>
            <p class="text-orange-100 text-xs mt-1">قطعة</p>
          </div>
          <div class="p-3 bg-white bg-opacity-20 rounded-xl">
            <Package :size="32" />
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Sales Trend Chart -->
      <div class="card">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
          <TrendingUp :size="24" class="text-primary-600" />
          اتجاه المبيعات
        </h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
          <canvas ref="salesTrendChart"></canvas>
        </div>
      </div>

      <!-- Paid Invoices Chart -->
      <div class="card">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
          <DollarSign :size="24" class="text-green-600" />
          الفواتير المدفوعة
        </h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
          <canvas ref="paidInvoicesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold">سجل المبيعات</h3>
        <div class="flex items-center gap-3">
          <div class="relative">
            <Search :size="18" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="بحث..."
              class="input pr-10 w-64"
            />
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>رقم الفاتورة</th>
              <th>التاريخ</th>
              <th>العميل</th>
              <th>المنتجات</th>
              <th>الإجمالي</th>
              <th>المبلغ المدفوع</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sale in filteredSales" :key="sale.id">
              <td class="font-mono font-bold text-primary-600">#{{ toLatinNumbers(sale.id) }}</td>
              <td>{{ formatDateLatin(sale.created_at) }}</td>
              <td>
                <div class="font-medium">{{ sale.customer?.name || 'عميل عادي' }}</div>
                <div class="text-xs text-gray-500">{{ sale.customer?.phone || '-' }}</div>
              </td>
              <td>{{ toLatinNumbers(sale.items_count) }} منتج</td>
              <td class="font-bold text-blue-600">{{ formatCurrencyLatin(sale.total) }}</td>
              <td class="font-bold text-green-600">{{ formatCurrencyLatin(sale.paid_sum) }}</td>
              <td>
                <span :class="[
                  'badge',
                  sale.status === 'paid' ? 'badge-success' :
                  sale.status === 'partially_paid' ? 'badge-warning' :
                  'badge-danger'
                ]">
                  {{ getStatusLabel(sale.status) }}
                </span>
              </td>
              <td>
                <div class="flex items-center gap-2">
                  <button @click="viewSaleDetails(sale)" class="text-blue-600 hover:text-blue-800" title="عرض التفاصيل">
                    <Eye :size="18" />
                  </button>
                  <button @click="downloadInvoice(sale.id)" class="text-green-600 hover:text-green-800" title="تحميل الفاتورة">
                    <Download :size="18" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSales.length === 0">
              <td colspan="8" class="text-center py-8 text-gray-500">
                لا توجد مبيعات في هذه الفترة
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="flex items-center justify-between mt-4 pt-4 border-t">
        <div class="text-sm text-gray-600">
          عرض {{ toLatinNumbers(pagination.from) }} - {{ toLatinNumbers(pagination.to) }} من {{ toLatinNumbers(pagination.total) }}
        </div>
        <div class="flex gap-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="btn btn-secondary"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
          >
            السابق
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="btn btn-secondary"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
          >
            التالي
          </button>
        </div>
      </div>
    </div>

    <!-- Sale Details Modal -->
    <div v-if="selectedSale" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="selectedSale = null">
      <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-primary-600">تفاصيل الفاتورة #{{ toLatinNumbers(selectedSale.id) }}</h3>
          <button @click="selectedSale = null" class="text-gray-400 hover:text-gray-600">
            <X :size="24" />
          </button>
        </div>

        <div class="space-y-4">
          <!-- Customer Info -->
          <div class="p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
              <User :size="20" class="text-blue-600" />
              <h4 class="font-bold">معلومات العميل</h4>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div><span class="text-gray-600">الاسم:</span> {{ selectedSale.customer?.name || 'عميل عادي' }}</div>
              <div><span class="text-gray-600">الهاتف:</span> {{ selectedSale.customer?.phone || '-' }}</div>
            </div>
          </div>

          <!-- Products -->
          <div>
            <h4 class="font-bold mb-3 flex items-center gap-2">
              <Package :size="20" class="text-primary-600" />
              المنتجات
            </h4>
            <div class="space-y-2">
              <div v-for="item in selectedSale.items" :key="item.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                  <div class="font-medium">{{ item.product_name }}</div>
                  <div class="text-sm text-gray-600">{{ toLatinNumbers(item.quantity) }} × {{ formatCurrencyLatin(item.price) }}</div>
                </div>
                <div class="font-bold text-blue-600">{{ formatCurrencyLatin(item.subtotal) }}</div>
              </div>
            </div>
          </div>

          <!-- Totals -->
          <div class="p-4 bg-gray-50 rounded-lg space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">المجموع الفرعي:</span>
              <span class="font-bold">{{ formatCurrencyLatin(selectedSale.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">الخصم:</span>
              <span class="font-bold text-red-600">- {{ formatCurrencyLatin(selectedSale.discount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">الضريبة:</span>
              <span class="font-bold">{{ formatCurrencyLatin(selectedSale.tax) }}</span>
            </div>
            <div class="flex justify-between text-lg pt-2 border-t">
              <span class="font-bold">الإجمالي:</span>
              <span class="font-bold text-primary-600">{{ formatCurrencyLatin(selectedSale.total) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-bold text-green-600">المبلغ المدفوع:</span>
              <span class="font-bold text-green-600">{{ formatCurrencyLatin(selectedSale.paid_sum) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-bold text-orange-600">المتبقي:</span>
              <span class="font-bold text-orange-600">{{ formatCurrencyLatin(selectedSale.balance_due) }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <button @click="downloadInvoice(selectedSale.id)" class="btn btn-primary flex-1">
              <Download :size="18" />
              تحميل الفاتورة
            </button>
            <button @click="selectedSale = null" class="btn btn-secondary flex-1">إغلاق</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { 
  TrendingUp, DollarSign, Package, BarChart3, Download, Filter, 
  Search, Eye, X, User 
} from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'
import Chart from 'chart.js/auto'
import * as XLSX from 'xlsx'

const toast = useToast()

// Data
const sales = ref([])
const selectedPeriod = ref('month') // Changed from 'today' to 'month' to show more data by default
const customDateRange = ref({
  start: '',
  end: ''
})
const searchQuery = ref('')
const selectedSale = ref(null)
const salesTrendChart = ref(null)
const paidInvoicesChart = ref(null)
let salesChart = null
let paidChart = null

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0
})

const stats = ref({
  total_sales: 0,
  total_orders: 0,
  paid_amount: 0,
  paid_count: 0,
  average_sale: 0,
  total_items: 0
})

const periods = [
  { label: 'اليوم', value: 'today' },
  { label: 'هذا الأسبوع', value: 'week' },
  { label: 'هذا الشهر', value: 'month' },
  { label: 'هذا العام', value: 'year' },
  { label: 'فترة مخصصة', value: 'custom' }
]

// Computed
const filteredSales = computed(() => {
  if (!searchQuery.value) return sales.value
  
  const query = searchQuery.value.toLowerCase()
  return sales.value.filter(sale => {
    return (
      sale.id.toString().includes(query) ||
      sale.customer?.name?.toLowerCase().includes(query) ||
      sale.customer?.phone?.includes(query)
    )
  })
})

// Methods
const fetchSalesData = async () => {
  try {
    const params = {
      page: pagination.value.current_page
    }
    
    // Add date filtering based on period
    const now = new Date()
    if (selectedPeriod.value === 'today') {
      const today = now.toISOString().split('T')[0]
      params.start_date = today
      params.end_date = today
    } else if (selectedPeriod.value === 'week') {
      const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
      params.start_date = weekAgo.toISOString().split('T')[0]
      params.end_date = now.toISOString().split('T')[0]
    } else if (selectedPeriod.value === 'month') {
      const monthStart = new Date(now.getFullYear(), now.getMonth(), 1)
      params.start_date = monthStart.toISOString().split('T')[0]
      params.end_date = now.toISOString().split('T')[0]
    } else if (selectedPeriod.value === 'year') {
      const yearStart = new Date(now.getFullYear(), 0, 1)
      params.start_date = yearStart.toISOString().split('T')[0]
      params.end_date = now.toISOString().split('T')[0]
    } else if (selectedPeriod.value === 'custom') {
      if (customDateRange.value.start && customDateRange.value.end) {
        params.start_date = customDateRange.value.start
        params.end_date = customDateRange.value.end
      }
    }
    
    console.log('Fetching sales with params:', params)
    
    // Use getSales instead of getSalesReport
    const response = await api.getSales(params)
    console.log('Sales response FULL:', JSON.stringify(response.data, null, 2))
    console.log('Is Array?', Array.isArray(response.data))
    console.log('Has data key?', !!response.data.data)
    console.log('Data type:', typeof response.data)
    
    // Handle different response formats
    // Backend returns: { sales: { data: [...], meta: {...} }, counts: {...} }
    if (response.data.sales) {
      // Paginated response with 'sales' key
      if (response.data.sales.data) {
        sales.value = response.data.sales.data
        console.log('Using sales.data format, length:', sales.value.length)
        
        // Update pagination from sales meta
        if (response.data.sales.meta || response.data.sales.current_page) {
          pagination.value = {
            current_page: response.data.sales.current_page || response.data.sales.meta?.current_page || 1,
            last_page: response.data.sales.last_page || response.data.sales.meta?.last_page || 1,
            per_page: response.data.sales.per_page || response.data.sales.meta?.per_page || 20,
            total: response.data.sales.total || response.data.sales.meta?.total || 0,
            from: response.data.sales.from || response.data.sales.meta?.from || 0,
            to: response.data.sales.to || response.data.sales.meta?.to || 0
          }
        }
      } else {
        sales.value = response.data.sales
        console.log('Using sales array format, length:', sales.value.length)
      }
    } else if (Array.isArray(response.data)) {
      sales.value = response.data
      console.log('Using array format, length:', sales.value.length)
    } else if (response.data.data) {
      sales.value = response.data.data
      console.log('Using data key format, length:', sales.value.length)
      
      // Update pagination if available
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          per_page: response.data.meta.per_page,
          total: response.data.meta.total,
          from: response.data.meta.from,
          to: response.data.meta.to
        }
      }
    } else {
      sales.value = []
      console.log('No data found, setting empty array')
    }
    
    calculateStats()
    await nextTick()
    renderCharts()
  } catch (error) {
    console.error('Failed to fetch sales:', error)
    console.error('Error details:', error.response?.data)
    toast.error('فشل تحميل بيانات المبيعات')
    sales.value = []
  }
}

const calculateStats = () => {
  console.log('Calculating stats from', sales.value.length, 'sales')
  
  const totalSales = sales.value.reduce((sum, sale) => sum + parseFloat(sale.total || 0), 0)
  const totalItems = sales.value.reduce((sum, sale) => sum + parseInt(sale.items_count || 0), 0)
  
  // Calculate total paid amount (including partial payments)
  // Sum all paid_sum values from all invoices
  const totalPaidAmount = sales.value.reduce((sum, sale) => sum + parseFloat(sale.paid_sum || 0), 0)
  
  // Count invoices that have any payment (paid or partially_paid)
  const paidInvoicesCount = sales.value.filter(sale => 
    sale.status === 'paid' || sale.status === 'partially_paid'
  ).length
  
  stats.value = {
    total_sales: totalSales,
    total_orders: sales.value.length,
    paid_amount: totalPaidAmount,  // Total of all payments
    paid_count: paidInvoicesCount,  // Count of paid + partially paid
    average_sale: sales.value.length > 0 ? (totalSales / sales.value.length) : 0,
    total_items: totalItems
  }
  
  console.log('Stats calculated:', stats.value)
}

const renderCharts = () => {
  // Sales Trend Chart
  if (salesChart) salesChart.destroy()
  if (salesTrendChart.value) {
    const ctx = salesTrendChart.value.getContext('2d')
    const dates = sales.value.slice(0, 10).reverse().map(s => new Date(s.created_at).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric' }))
    const amounts = sales.value.slice(0, 10).reverse().map(s => parseFloat(s.total))
    
    salesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: dates,
        datasets: [{
          label: 'المبيعات',
          data: amounts,
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        }
      }
    })
  }
  
  // Paid Invoices Chart
  if (paidChart) paidChart.destroy()
  if (paidInvoicesChart.value) {
    // Group all payments by date (including partial payments)
    const paidByDate = {}
    sales.value.forEach(sale => {
      const paidAmount = parseFloat(sale.paid_sum || 0)
      if (paidAmount > 0) {  // Only include if there's any payment
        const date = new Date(sale.created_at).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric' })
        if (!paidByDate[date]) {
          paidByDate[date] = 0
        }
        paidByDate[date] += paidAmount
      }
    })
    
    const dates = Object.keys(paidByDate).slice(-10)
    const amounts = dates.map(date => paidByDate[date])
    
    const ctx = paidInvoicesChart.value.getContext('2d')
    paidChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: dates,
        datasets: [{
          label: 'المبلغ المدفوع',
          data: amounts,
          backgroundColor: 'rgba(16, 185, 129, 0.8)',
          borderColor: 'rgb(16, 185, 129)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    pagination.value.current_page = page
    fetchSalesData()
  }
}

const viewSaleDetails = async (sale) => {
  try {
    const response = await api.getSale(sale.id)
    selectedSale.value = response.data
  } catch (error) {
    console.error('Failed to fetch sale details:', error)
    toast.error('فشل تحميل تفاصيل الفاتورة')
  }
}

const downloadInvoice = async (id) => {
  try {
    // Don't show loading toast, just download directly
    await api.downloadInvoicePdf(id)
    // Success toast will show after download completes
  } catch (error) {
    console.error('PDF download error:', error)
    toast.error('فشل تحميل الفاتورة')
  }
}

const exportToExcel = () => {
  try {
    // Prepare data for export (optimized)
    const exportData = sales.value.map(sale => ({
      'رقم الفاتورة': sale.invoice_number || `#${sale.id}`,
      'التاريخ': formatDateLatin(sale.created_at),
      'العميل': sale.customer?.name || 'عميل عادي',
      'الهاتف': sale.customer?.phone || '-',
      'عدد المنتجات': sale.items_count,
      'المجموع الفرعي': parseFloat(sale.subtotal),
      'الخصم': parseFloat(sale.discount),
      'الضريبة': parseFloat(sale.tax),
      'الإجمالي': parseFloat(sale.total),
      'المبلغ المدفوع': parseFloat(sale.paid_sum),
      'المتبقي': parseFloat(sale.balance_due),
      'الحالة': getStatusLabel(sale.status),
      'طريقة الدفع': sale.payment_method === 'cash' ? 'نقدي' : sale.payment_method === 'card' ? 'بطاقة' : sale.payment_method
    }))
    
    // Create worksheet
    const ws = XLSX.utils.json_to_sheet(exportData)
    
    // Set column widths
    ws['!cols'] = [
      { wch: 20 }, { wch: 20 }, { wch: 25 }, { wch: 15 },
      { wch: 12 }, { wch: 15 }, { wch: 10 }, { wch: 10 },
      { wch: 15 }, { wch: 15 }, { wch: 12 }, { wch: 15 }, { wch: 15 }
    ]
    
    // Create workbook and download immediately
    const wb = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(wb, ws, 'المبيعات')
    
    // Generate filename
    const periodLabel = periods.find(p => p.value === selectedPeriod.value)?.label || 'المبيعات'
    const filename = `تقرير_المبيعات_${periodLabel}_${new Date().toISOString().split('T')[0]}.xlsx`
    
    // Download (this is synchronous and fast)
    XLSX.writeFile(wb, filename)
    
    // Show success after download starts
    toast.success('تم تصدير البيانات بنجاح!')
  } catch (error) {
    console.error('Excel export error:', error)
    toast.error('فشل تصدير البيانات')
  }
}

const getStatusLabel = (status) => {
  const labels = {
    'paid': 'مدفوع',
    'partially_paid': 'مدفوع جزئياً',
    'issued': 'صادر',
    'void': 'ملغي',
    'pending': 'معلق',
    'cancelled': 'ملغي'
  }
  return labels[status] || status
}

const formatDateLatin = (value) => {
  try {
    const d = new Date(value)
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`
  } catch {
    return ''
  }
}

onMounted(() => {
  fetchSalesData()
})
</script>
