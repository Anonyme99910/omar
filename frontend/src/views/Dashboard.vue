<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-xs sm:text-sm">مبيعات اليوم</p>
            <h3 class="text-2xl sm:text-3xl font-bold mt-2">{{ formatCurrency(stats.today?.sales || 0) }}</h3>
            <p class="text-blue-100 text-xs sm:text-sm mt-1">{{ toLatinNumbers(stats.today?.orders || 0) }} طلب</p>
          </div>
          <DollarSign :size="40" class="text-blue-200 sm:w-12 sm:h-12" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-xs sm:text-sm">مبيعات الشهر</p>
            <h3 class="text-2xl sm:text-3xl font-bold mt-2">{{ formatCurrency(stats.month?.sales || 0) }}</h3>
            <p class="text-green-100 text-xs sm:text-sm mt-1">{{ toLatinNumbers(stats.month?.orders || 0) }} طلب</p>
          </div>
          <TrendingUp :size="40" class="text-green-200 sm:w-12 sm:h-12" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-xs sm:text-sm">إجمالي المنتجات</p>
            <h3 class="text-2xl sm:text-3xl font-bold mt-2">{{ toLatinNumbers(stats.inventory?.total_products || 0) }}</h3>
            <p class="text-purple-100 text-xs sm:text-sm mt-1">{{ formatCurrency(stats.inventory?.net_worth || 0) }}</p>
          </div>
          <Package :size="40" class="text-purple-200 sm:w-12 sm:h-12" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-red-500 to-red-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-red-100 text-xs sm:text-sm">تنبيهات المخزون</p>
            <h3 class="text-2xl sm:text-3xl font-bold mt-2">{{ toLatinNumbers(stats.inventory?.low_stock || 0) }}</h3>
            <p class="text-red-100 text-xs sm:text-sm mt-1">منتج منخفض</p>
          </div>
          <AlertTriangle :size="40" class="text-red-200 sm:w-12 sm:h-12" />
        </div>
      </div>
    </div>

    <!-- Stock Value Card -->
    <div class="card bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200">
      <h3 class="text-lg font-bold mb-4 text-amber-900 flex items-center gap-2">
        💰 قيمة المخزون والربح المتوقع
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border border-amber-200">
          <p class="text-sm text-gray-600 mb-1">تكلفة المخزون</p>
          <p class="text-2xl font-bold text-red-600">{{ formatCurrency(stats.inventory?.stock_cost || 0) }}</p>
          <p class="text-xs text-gray-500 mt-1">إجمالي تكلفة الإنتاج</p>
        </div>
        <div class="bg-white rounded-lg p-4 border border-amber-200">
          <p class="text-sm text-gray-600 mb-1">قيمة البيع المتوقعة</p>
          <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(stats.inventory?.total_value || 0) }}</p>
          <p class="text-xs text-gray-500 mt-1">بسعر القطاعي</p>
        </div>
        <div class="bg-white rounded-lg p-4 border border-green-200 bg-green-50">
          <p class="text-sm text-gray-600 mb-1">الربح المتوقع</p>
          <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.inventory?.potential_profit || 0) }}</p>
          <p class="text-xs text-gray-500 mt-1">{{ calculateProfitMargin() }}% هامش ربح</p>
        </div>
      </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
      <!-- Top Products -->
      <div class="card">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
          <TrendingUp :size="24" class="text-primary-600" />
          المنتجات الأكثر مبيعاً
        </h3>
        <div class="space-y-3">
          <div
            v-for="product in stats.top_products"
            :key="product.product_id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ product.product?.name_ar }}</p>
              <p class="text-xs sm:text-sm text-gray-500 truncate">{{ product.product?.category?.name_ar }}</p>
            </div>
            <div class="text-left ml-2">
              <p class="font-bold text-primary-600 text-sm sm:text-base whitespace-nowrap">{{ toLatinNumbers(product.total_sold) }} قطعة</p>
            </div>
          </div>
          <div v-if="!stats.top_products?.length" class="text-center py-8 text-gray-500">
            لا توجد بيانات
          </div>
        </div>
      </div>

      <!-- Top Customers -->
      <div class="card">
        <h3 class="text-lg sm:text-xl font-bold mb-4 flex items-center gap-2">
          <Users :size="24" class="text-primary-600" />
          أفضل العملاء
        </h3>
        <div class="space-y-3">
          <div
            v-for="customer in stats.top_customers"
            :key="customer.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ customer.name }}</p>
              <p class="text-xs sm:text-sm text-gray-500">{{ toLatinNumbers(customer.phone) }}</p>
            </div>
            <div class="text-left ml-2">
              <p class="font-bold text-green-600 text-sm sm:text-base whitespace-nowrap">{{ formatCurrency(customer.total_purchases) }}</p>
              <p class="text-xs sm:text-sm text-gray-500">{{ toLatinNumbers(customer.total_orders) }} طلب</p>
            </div>
          </div>
          <div v-if="!stats.top_customers?.length" class="text-center py-8 text-gray-500">
            لا توجد بيانات
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { DollarSign, TrendingUp, Package, AlertTriangle, Users } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers } from '@/utils/numbers'

const toast = useToast()
const stats = ref({})

const formatCurrency = (value) => {
  const formatted = new Intl.NumberFormat('en-EG', {
    style: 'currency',
    currency: 'EGP'
  }).format(value)
  return toLatinNumbers(formatted)
}

const calculateProfitMargin = () => {
  const cost = stats.value.inventory?.stock_cost || 0
  const value = stats.value.inventory?.total_value || 0
  if (cost === 0 || value === 0) return '0'
  const margin = ((value - cost) / value * 100).toFixed(1)
  return toLatinNumbers(margin)
}

const fetchDashboard = async () => {
  try {
    const response = await api.getDashboard()
    stats.value = response.data
  } catch (error) {
    toast.error('فشل تحميل البيانات')
  }
}

onMounted(() => {
  fetchDashboard()
  
  // Auto-refresh every 5 minutes to keep data fresh
  const refreshInterval = setInterval(() => {
    fetchDashboard()
  }, 5 * 60 * 1000) // 5 minutes
  
  // Cleanup on unmount
  onUnmounted(() => {
    clearInterval(refreshInterval)
  })
})
</script>
