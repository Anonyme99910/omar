<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <input
        v-model="startDate"
        type="date"
        class="input w-48"
      />
      <span>إلى</span>
      <input
        v-model="endDate"
        type="date"
        class="input w-48"
      />
      <button @click="fetchSales" class="btn btn-primary">بحث</button>
    </div>

    <div class="card overflow-hidden">
      <table class="table">
        <thead>
          <tr>
            <th>رقم الفاتورة</th>
            <th>التاريخ</th>
            <th>العميل</th>
            <th>المبلغ</th>
            <th>طريقة الدفع</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in sales" :key="sale.id">
            <td class="font-mono font-bold">{{ sale.invoice_number }}</td>
            <td>{{ formatDate(sale.created_at) }}</td>
            <td>{{ sale.customer?.name || 'عميل عادي' }}</td>
            <td class="font-bold text-green-600">{{ formatCurrency(sale.total) }}</td>
            <td>
              <span class="badge badge-info">
                {{ getPaymentMethodLabel(sale.payment_method) }}
              </span>
            </td>
            <td>
              <span
                :class="{
                  'badge-success': sale.status === 'completed',
                  'badge-warning': sale.status === 'pending',
                  'badge-danger': sale.status === 'cancelled'
                }"
                class="badge"
              >
                {{ getStatusLabel(sale.status) }}
              </span>
            </td>
            <td>
              <div class="flex items-center gap-2">
                <button @click="viewSale(sale)" class="text-blue-600 hover:text-blue-800">
                  <Eye :size="18" />
                </button>
                <button
                  v-if="sale.status === 'completed'"
                  @click="printInvoice(sale)"
                  class="text-green-600 hover:text-green-800"
                >
                  <Printer :size="18" />
                </button>
                <button
                  v-if="sale.status === 'completed'"
                  @click="cancelSale(sale.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  <XCircle :size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- View Sale Modal -->
    <div v-if="viewingSale" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold">تفاصيل الفاتورة</h3>
          <button @click="viewingSale = null" class="text-gray-500 hover:text-gray-700">
            <X :size="24" />
          </button>
        </div>

        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">رقم الفاتورة</p>
              <p class="font-bold">{{ viewingSale.invoice_number }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">التاريخ</p>
              <p class="font-bold">{{ formatDate(viewingSale.created_at) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">العميل</p>
              <p class="font-bold">{{ viewingSale.customer?.name || 'عميل عادي' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">طريقة الدفع</p>
              <p class="font-bold">{{ getPaymentMethodLabel(viewingSale.payment_method) }}</p>
            </div>
          </div>

          <div class="border-t pt-4">
            <h4 class="font-bold mb-3">المنتجات</h4>
            <table class="table">
              <thead>
                <tr>
                  <th>المنتج</th>
                  <th>الكمية</th>
                  <th>السعر</th>
                  <th>الإجمالي</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in viewingSale.items" :key="item.id">
                  <td>{{ item.product?.name_ar }}</td>
                  <td>{{ item.quantity }}</td>
                  <td>{{ formatCurrency(item.unit_price) }}</td>
                  <td class="font-bold">{{ formatCurrency(item.total_price) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between">
              <span>المجموع الفرعي:</span>
              <span class="font-bold">{{ formatCurrency(viewingSale.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span>الضريبة:</span>
              <span class="font-bold">{{ formatCurrency(viewingSale.tax) }}</span>
            </div>
            <div class="flex justify-between">
              <span>الخصم:</span>
              <span class="font-bold">{{ formatCurrency(viewingSale.discount) }}</span>
            </div>
            <div class="flex justify-between text-lg border-t pt-2">
              <span class="font-bold">الإجمالي:</span>
              <span class="font-bold text-primary-600">{{ formatCurrency(viewingSale.total) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Eye, Printer, XCircle, X } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'

const toast = useToast()
const sales = ref([])
const viewingSale = ref(null)
const startDate = ref('')
const endDate = ref('')

const formatCurrency = (value) => formatCurrencyLatin(value)

const formatDate = (date) => {
  const d = new Date(date)
  const s = d.toLocaleString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
  return toLatinNumbers(s)
}

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'نقدي',
    card: 'بطاقة',
    transfer: 'تحويل'
  }
  return labels[method] || method
}

const getStatusLabel = (status) => {
  const labels = {
    completed: 'مكتمل',
    pending: 'قيد الانتظار',
    cancelled: 'ملغي'
  }
  return labels[status] || status
}

const fetchSales = async () => {
  try {
    const params = {}
    if (startDate.value) params.start_date = startDate.value
    if (endDate.value) params.end_date = endDate.value
    
    const response = await api.getSales(params)
    sales.value = response.data
  } catch (error) {
    toast.error('فشل تحميل المبيعات')
  }
}

const viewSale = async (sale) => {
  try {
    const response = await api.getSale(sale.id)
    viewingSale.value = response.data
  } catch (error) {
    toast.error('فشل تحميل تفاصيل الفاتورة')
  }
}

const cancelSale = async (id) => {
  if (!confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟ سيتم إرجاع المنتجات للمخزون.')) return
  
  try {
    await api.cancelSale(id)
    toast.success('تم إلغاء الفاتورة بنجاح')
    fetchSales()
  } catch (error) {
    toast.error('فشل إلغاء الفاتورة')
  }
}

const printInvoice = (sale) => {
  const printWindow = window.open('', '_blank')
  const itemsHtml = sale.items?.map(item => 
    `<tr>
      <td>${item.product?.name_ar}</td>
      <td>${item.quantity}</td>
      <td>${item.unit_price} جنيه</td>
      <td>${item.total_price} جنيه</td>
    </tr>`
  ).join('') || ''
  
  const customerHtml = sale.customer ? `<p><strong>العميل:</strong> ${sale.customer.name}</p>` : ''
  
  const html = `<html dir="rtl">
    <head>
      <title>فاتورة ${sale.invoice_number}</title>
      <style>
        body { font-family: Arial; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background-color: #f2f2f2; }
        .totals { margin-top: 20px; text-align: left; }
        .totals div { margin: 5px 0; }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>متجر العطور</h1>
        <h2>فاتورة بيع</h2>
      </div>
      <div class="invoice-details">
        <p><strong>رقم الفاتورة:</strong> ${sale.invoice_number}</p>
        <p><strong>التاريخ:</strong> ${formatDate(sale.created_at)}</p>
        ${customerHtml}
      </div>
      <table>
        <thead>
          <tr>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الإجمالي</th>
          </tr>
        </thead>
        <tbody>${itemsHtml}</tbody>
      </table>
      <div class="totals">
        <div><strong>المجموع الفرعي:</strong> ${sale.subtotal} جنيه</div>
        <div><strong>الضريبة:</strong> ${sale.tax} جنيه</div>
        <div><strong>الخصم:</strong> ${sale.discount} جنيه</div>
        <div style="font-size: 20px;"><strong>الإجمالي:</strong> ${sale.total} جنيه</div>
        <div><strong>طريقة الدفع:</strong> ${getPaymentMethodLabel(sale.payment_method)}</div>
      </div>
    </body>
  </html>`
  
  printWindow.document.write(html)
  printWindow.document.close()
  printWindow.print()
}

onMounted(() => {
  // Set default dates (last 30 days)
  const today = new Date()
  const thirtyDaysAgo = new Date(today)
  thirtyDaysAgo.setDate(today.getDate() - 30)
  
  endDate.value = today.toISOString().split('T')[0]
  startDate.value = thirtyDaysAgo.toISOString().split('T')[0]
  
  fetchSales()
})
</script>
