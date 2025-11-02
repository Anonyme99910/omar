<template>
  <div class="invoices-container">
    <div class="header">
      <h1>الفواتير</h1>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.value"
        :class="['tab', { active: activeTab === tab.value }]"
        @click="activeTab = tab.value"
      >
        {{ tab.label }}
        <span v-if="counts[tab.value]" class="badge">{{ counts[tab.value] }}</span>
      </button>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input 
        v-model="filters.search" 
        type="text" 
        placeholder="بحث برقم الفاتورة أو العميل..."
        class="search-input"
        @keyup.enter="loadInvoices"
      >
      <div class="date-filter">
        <label>من تاريخ:</label>
        <input v-model="filters.startDate" type="date" class="date-input">
      </div>
      <div class="date-filter">
        <label>إلى تاريخ:</label>
        <input v-model="filters.endDate" type="date" class="date-input">
      </div>
      <button @click="loadInvoices" class="btn-primary">بحث</button>
      <button @click="resetFilters" class="btn-secondary">مسح الفلاتر</button>
    </div>

    <!-- Table -->
    <div class="table-container">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>رقم الفاتورة</th>
              <th class="hide-mobile">العميل</th>
              <th class="hide-mobile">التاريخ</th>
              <th>الإجمالي</th>
              <th class="hide-tablet">المدفوع</th>
              <th class="hide-tablet">المتبقي</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices" :key="invoice.id">
              <td>
                <div class="invoice-number">{{ invoice.invoice_number }}</div>
                <div class="mobile-info">
                  <small>{{ invoice.customer?.name || 'عميل عادي' }}</small>
                  <small>{{ formatDate(invoice.issue_date || invoice.created_at) }}</small>
                </div>
              </td>
              <td class="hide-mobile">{{ invoice.customer?.name || 'عميل عادي' }}</td>
              <td class="hide-mobile">{{ formatDate(invoice.issue_date || invoice.created_at) }}</td>
              <td>
                <div class="amount-cell">
                  {{ formatCurrency(invoice.total) }}
                  <div class="mobile-amounts">
                    <small>مدفوع: {{ formatCurrency(invoice.paid_sum) }}</small>
                    <small v-if="invoice.balance_due > 0">متبقي: {{ formatCurrency(invoice.balance_due) }}</small>
                  </div>
                </div>
              </td>
              <td class="hide-tablet">{{ formatCurrency(invoice.paid_sum) }}</td>
              <td class="hide-tablet">{{ formatCurrency(invoice.balance_due) }}</td>
              <td>
                <span :class="['status-badge', invoice.status]">
                  {{ getStatusLabel(invoice.status) }}
                </span>
              </td>
              <td class="actions">
                <button @click="viewInvoice(invoice.id)" class="btn-icon" title="عرض">
                  <Eye :size="18" />
                </button>
                <button @click="downloadPDF(invoice.id)" class="btn-icon hide-mobile" title="تحميل PDF">
                  <Download :size="18" />
                </button>
                <button 
                  v-if="invoice.status !== 'void'" 
                  @click="voidInvoice(invoice.id)" 
                  class="btn-icon btn-danger hide-mobile" 
                  title="إلغاء"
                >
                  <XCircle :size="18" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="pagination">
      <button 
        @click="changePage(pagination.current_page - 1)" 
        :disabled="pagination.current_page === 1"
        class="btn-secondary"
      >
        السابق
      </button>
      <span>صفحة {{ pagination.current_page }} من {{ pagination.last_page }}</span>
      <button 
        @click="changePage(pagination.current_page + 1)" 
        :disabled="pagination.current_page === pagination.last_page"
        class="btn-secondary"
      >
        التالي
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Eye, Download, XCircle } from 'lucide-vue-next'
import api from '../../services/api'
import { useToast } from 'vue-toastification'

const router = useRouter()
const toast = useToast()

const activeTab = ref('all')
const invoices = ref([])
const counts = ref({})
const filters = ref({
  search: '',
  startDate: '',
  endDate: ''
})
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0
})

const tabs = [
  { label: 'الكل', value: 'all' },
  { label: 'غير مدفوع', value: 'unpaid' },
  { label: 'مدفوع جزئياً', value: 'partially_paid' },
  { label: 'مدفوع', value: 'paid' },
  { label: 'ملغي', value: 'void' }
]

const loadInvoices = async (page = 1) => {
  try {
    const params = {
      page,
      status: activeTab.value !== 'all' ? activeTab.value : undefined,
      start_date: filters.value.startDate || undefined,
      end_date: filters.value.endDate || undefined
    }

    const response = await api.getSales(params)
    invoices.value = response.data.sales.data
    counts.value = response.data.counts
    pagination.value = {
      current_page: response.data.sales.current_page,
      last_page: response.data.sales.last_page,
      per_page: response.data.sales.per_page,
      total: response.data.sales.total
    }
  } catch (error) {
    toast.error('فشل تحميل الفواتير')
    console.error(error)
  }
}

const viewInvoice = (id) => {
  router.push(`/invoices/${id}`)
}

const downloadPDF = async (id) => {
  try {
    await api.downloadInvoicePdf(id)
    toast.success('تم تحميل الفاتورة')
  } catch (error) {
    toast.error('فشل تحميل الفاتورة')
  }
}

const voidInvoice = async (id) => {
  if (!confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')) return

  try {
    await api.voidSale(id)
    toast.success('تم إلغاء الفاتورة')
    loadInvoices(pagination.value.current_page)
  } catch (error) {
    toast.error('فشل إلغاء الفاتورة')
  }
}

const changePage = (page) => {
  loadInvoices(page)
}

const resetFilters = () => {
  filters.value = {
    search: '',
    startDate: '',
    endDate: ''
  }
  loadInvoices()
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-GB')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 2
  }).format(amount).replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'مسودة',
    issued: 'صادر',
    partially_paid: 'مدفوع جزئياً',
    paid: 'مدفوع',
    void: 'ملغي'
  }
  return labels[status] || status
}

watch(activeTab, () => {
  loadInvoices()
})

onMounted(() => {
  loadInvoices()
})
</script>

<style scoped>
.invoices-container {
  padding: 20px;
  direction: rtl;
}

.header {
  margin-bottom: 20px;
}

.header h1 {
  font-size: 24px;
  color: #1e293b;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  border-bottom: 2px solid #e2e8f0;
}

.tab {
  padding: 10px 20px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 14px;
  color: #64748b;
  transition: all 0.3s;
  position: relative;
}

.tab.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
}

.tab .badge {
  background: #3b82f6;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  margin-right: 5px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.search-input,
.date-input {
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
}

.search-input {
  flex: 1;
  min-width: 200px;
}

.date-filter {
  display: flex;
  align-items: center;
  gap: 8px;
}

.date-filter label {
  font-size: 14px;
  color: #475569;
  white-space: nowrap;
}

.table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

th {
  background: #f8fafc;
  padding: 12px;
  text-align: right;
  font-weight: 600;
  color: #475569;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

td {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
}

.invoice-number {
  font-weight: 600;
  color: #1e293b;
}

.mobile-info {
  display: none;
  flex-direction: column;
  gap: 4px;
  margin-top: 4px;
}

.mobile-info small {
  color: #64748b;
  font-size: 12px;
}

.amount-cell {
  font-weight: 600;
}

.mobile-amounts {
  display: none;
  flex-direction: column;
  gap: 2px;
  margin-top: 4px;
}

.mobile-amounts small {
  color: #64748b;
  font-size: 11px;
  font-weight: normal;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.issued {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.partially_paid {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.paid {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.void {
  background: #fee2e2;
  color: #991b1b;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-icon {
  padding: 6px;
  background: #f1f5f9;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #e2e8f0;
}

.btn-icon.btn-danger:hover {
  background: #fee2e2;
  color: #dc2626;
}

.btn-primary,
.btn-secondary {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hide-tablet {
    display: none !important;
  }
  
  .mobile-amounts {
    display: flex;
  }
}

@media (max-width: 768px) {
  .invoices-container {
    padding: 10px;
  }
  
  .header h1 {
    font-size: 20px;
  }
  
  .tabs {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  
  .tabs::-webkit-scrollbar {
    display: none;
  }
  
  .tab {
    padding: 8px 16px;
    font-size: 13px;
    white-space: nowrap;
  }
  
  .filters {
    flex-direction: column;
    gap: 10px;
  }
  
  .search-input,
  .date-filter,
  .btn-primary,
  .btn-secondary {
    width: 100%;
  }
  
  .date-filter {
    justify-content: space-between;
  }
  
  .hide-mobile {
    display: none !important;
  }
  
  .mobile-info {
    display: flex;
  }
  
  table {
    min-width: 100%;
  }
  
  th, td {
    padding: 8px;
    font-size: 13px;
  }
  
  .actions {
    display: flex;
    justify-content: center;
  }
  
  .pagination {
    gap: 10px;
    font-size: 14px;
  }
  
  .pagination button {
    padding: 6px 12px;
    font-size: 13px;
  }
}

@media (max-width: 480px) {
  .header h1 {
    font-size: 18px;
  }
  
  .tab {
    padding: 6px 12px;
    font-size: 12px;
  }
  
  .tab .badge {
    padding: 1px 6px;
    font-size: 10px;
  }
  
  th, td {
    padding: 6px;
    font-size: 12px;
  }
  
  .status-badge {
    padding: 3px 8px;
    font-size: 10px;
  }
  
  .btn-icon {
    padding: 4px;
  }
}
</style>
