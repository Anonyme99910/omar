<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <input v-model="startDate" type="date" class="input w-48" />
      <span>إلى</span>
      <input v-model="endDate" type="date" class="input w-48" />
      <button @click="fetchReports" class="btn btn-primary">عرض التقارير</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <p class="text-blue-100 text-sm">إجمالي المبيعات</p>
        <h3 class="text-3xl font-bold mt-2">{{ formatCurrency(salesReport.summary?.total_sales || 0) }}</h3>
      </div>
      <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <p class="text-green-100 text-sm">إجمالي الأرباح</p>
        <h3 class="text-3xl font-bold mt-2">{{ formatCurrency(salesReport.summary?.total_profit || 0) }}</h3>
      </div>
      <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <p class="text-purple-100 text-sm">عدد الطلبات</p>
        <h3 class="text-3xl font-bold mt-2">{{ salesReport.summary?.total_orders || 0 }}</h3>
      </div>
      <div class="card bg-gradient-to-br from-yellow-500 to-yellow-600 text-white">
        <p class="text-yellow-100 text-sm">متوسط الطلب</p>
        <h3 class="text-3xl font-bold mt-2">{{ formatCurrency(salesReport.summary?.average_order_value || 0) }}</h3>
      </div>
    </div>

    <div class="card">
      <h3 class="text-xl font-bold mb-4">المنتجات الأكثر مبيعاً</h3>
      <table class="table">
        <thead>
          <tr>
            <th>المنتج</th>
            <th>الكمية المباعة</th>
            <th>الإيرادات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in productReport.best_selling" :key="item.product_id">
            <td>{{ item.product?.name_ar }}</td>
            <td class="font-bold">{{ item.total_sold }}</td>
            <td class="font-bold text-green-600">{{ formatCurrency(item.total_revenue) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()
const salesReport = ref({})
const productReport = ref({})
const startDate = ref('')
const endDate = ref('')

const formatCurrency = (value) => {
  return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP' }).format(value)
}

const fetchReports = async () => {
  try {
    const params = { start_date: startDate.value, end_date: endDate.value }
    const [sales, products] = await Promise.all([
      api.getSalesReport(params),
      api.getProductReport(params)
    ])
    salesReport.value = sales.data
    productReport.value = products.data
  } catch (error) {
    toast.error('فشل تحميل التقارير')
  }
}

onMounted(() => {
  const today = new Date()
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1)
  endDate.value = today.toISOString().split('T')[0]
  startDate.value = firstDay.toISOString().split('T')[0]
  fetchReports()
})
</script>
