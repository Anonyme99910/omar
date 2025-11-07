<template>
  <div class="invoice-detail" v-if="invoice">
    <div class="header">
      <button @click="$router.back()" class="btn-back">
        <ArrowRight :size="20" />
        رجوع
      </button>
      <h1>فاتورة {{ invoice.invoice_number }}</h1>
      <span :class="['status-badge', invoice.status]">
        {{ getStatusLabel(invoice.status) }}
      </span>
    </div>

    <div class="content">
      <!-- Invoice Info -->
      <div class="card">
        <h2>معلومات الفاتورة</h2>
        <div class="info-grid">
          <div class="info-item">
            <label>رقم الفاتورة:</label>
            <span>{{ invoice.invoice_number }}</span>
          </div>
          <div class="info-item">
            <label>التاريخ:</label>
            <span>{{ formatDate(invoice.issue_date || invoice.created_at) }}</span>
          </div>
          <div class="info-item">
            <label>العميل:</label>
            <span>{{ invoice.customer?.name || 'عميل عادي' }}</span>
          </div>
          <div class="info-item">
            <label>الهاتف:</label>
            <span>{{ invoice.customer?.phone || '-' }}</span>
          </div>
        </div>
      </div>

      <!-- Items -->
      <div class="card">
        <h2>المنتجات</h2>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>المنتج</th>
              <th>الكمية</th>
              <th>السعر</th>
              <th>الإجمالي</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in invoice.items" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.product?.name_ar }}</td>
              <td>{{ item.quantity }}</td>
              <td>{{ formatCurrency(item.unit_price) }}</td>
              <td>{{ formatCurrency(item.total_price) }}</td>
            </tr>
          </tbody>
        </table>

        <div class="totals">
          <div class="total-row">
            <span>المجموع الفرعي:</span>
            <span>{{ formatCurrency(invoice.subtotal) }}</span>
          </div>
          <div class="total-row" v-if="invoice.discount > 0">
            <span>الخصم:</span>
            <span>-{{ formatCurrency(invoice.discount) }}</span>
          </div>
          <div class="total-row" v-if="invoice.tax > 0">
            <span>الضريبة:</span>
            <span>{{ formatCurrency(invoice.tax) }}</span>
          </div>
          <div class="total-row" v-if="invoice.shipping > 0">
            <span>الشحن:</span>
            <span>{{ formatCurrency(invoice.shipping) }}</span>
          </div>
          <div class="total-row total">
            <span>الإجمالي:</span>
            <span>{{ formatCurrency(invoice.total) }}</span>
          </div>
          <div class="total-row paid">
            <span>المدفوع:</span>
            <span>{{ formatCurrency(invoice.paid_sum) }}</span>
          </div>
          <div class="total-row balance" v-if="invoice.balance_due > 0">
            <span>المتبقي:</span>
            <span>{{ formatCurrency(invoice.balance_due) }}</span>
          </div>
        </div>
      </div>

      <!-- Payments -->
      <div class="card">
        <div class="card-header">
          <h2>المدفوعات</h2>
          <button 
            v-if="invoice.balance_due > 0 && invoice.status !== 'void'" 
            @click="showPaymentModal = true"
            class="btn-primary"
          >
            <Plus :size="18" />
            تسجيل دفعة
          </button>
        </div>

        <div v-if="payments.length === 0" class="empty-state">
          لا توجد مدفوعات مسجلة
        </div>

        <table v-else>
          <thead>
            <tr>
              <th>التاريخ</th>
              <th>المبلغ</th>
              <th>الطريقة</th>
              <th>المرجع</th>
              <th>الملاحظات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="payment in payments" :key="payment.id">
              <td>{{ formatDateTime(payment.paid_at) }}</td>
              <td>{{ formatCurrency(payment.amount) }}</td>
              <td>{{ getPaymentMethodLabel(payment.method) }}</td>
              <td>{{ payment.reference || '-' }}</td>
              <td>{{ payment.notes || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Actions -->
      <div class="actions-bar">
        <button @click="downloadPDF" class="btn-primary">
          <Download :size="18" />
          تحميل PDF
        </button>
        <button @click="shareWhatsApp" class="btn-success">
          <Share2 :size="18" />
          مشاركة واتساب
        </button>
        <button 
          v-if="invoice.status !== 'void'" 
          @click="voidInvoice" 
          class="btn-danger"
        >
          <XCircle :size="18" />
          إلغاء الفاتورة
        </button>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="modal-overlay" @click="showPaymentModal = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>تسجيل دفعة جديدة</h3>
          <button @click="showPaymentModal = false" class="btn-close">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>المبلغ *</label>
            <input 
              v-model.number="paymentForm.amount" 
              type="number" 
              step="0.01"
              :max="invoice.balance_due"
              placeholder="0.00"
            >
            <small>المتبقي: {{ formatCurrency(invoice.balance_due) }}</small>
          </div>
          <div class="form-group">
            <label>طريقة الدفع *</label>
            <select v-model="paymentForm.method">
              <option value="cash">نقدي</option>
              <option value="card">بطاقة</option>
              <option value="bank">تحويل بنكي</option>
              <option value="wallet">محفظة إلكترونية</option>
              <option value="cod">الدفع عند الاستلام</option>
            </select>
          </div>
          <div class="form-group">
            <label>المرجع</label>
            <input v-model="paymentForm.reference" type="text" placeholder="رقم المعاملة أو المرجع">
          </div>
          <div class="form-group">
            <label>ملاحظات</label>
            <textarea v-model="paymentForm.notes" rows="3" placeholder="ملاحظات إضافية"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showPaymentModal = false" class="btn-secondary">إلغاء</button>
          <button @click="recordPayment" class="btn-primary">حفظ</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowRight, Download, Share2, XCircle, Plus } from 'lucide-vue-next'
import api from '../../services/api'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const invoice = ref(null)
const payments = ref([])
const showPaymentModal = ref(false)
const paymentForm = ref({
  amount: 0,
  method: 'cash',
  reference: '',
  notes: ''
})

const loadInvoice = async () => {
  try {
    const response = await api.getSale(route.params.id)
    invoice.value = response.data
  } catch (error) {
    toast.error('فشل تحميل الفاتورة')
    console.error(error)
  }
}

const loadPayments = async () => {
  try {
    const response = await api.getPayments(route.params.id)
    payments.value = response.data
  } catch (error) {
    console.error('Failed to load payments:', error)
  }
}

const recordPayment = async () => {
  if (!paymentForm.value.amount || paymentForm.value.amount <= 0) {
    toast.error('الرجاء إدخال مبلغ صحيح')
    return
  }

  if (paymentForm.value.amount > invoice.value.balance_due) {
    toast.error('المبلغ أكبر من المتبقي')
    return
  }

  try {
    await api.recordPayment(route.params.id, paymentForm.value)
    toast.success('تم تسجيل الدفعة بنجاح')
    showPaymentModal.value = false
    paymentForm.value = {
      amount: 0,
      method: 'cash',
      reference: '',
      notes: ''
    }
    loadInvoice()
    loadPayments()
  } catch (error) {
    console.error('Payment error:', error)
    const errorMsg = error.response?.data?.message || error.response?.data?.error || 'فشل تسجيل الدفعة'
    toast.error(errorMsg)
    if (error.response?.data?.details) {
      console.error('Error details:', error.response.data.details)
    }
  }
}

const downloadPDF = async () => {
  try {
    await api.downloadInvoicePdf(route.params.id)
    toast.success('تم تحميل الفاتورة')
  } catch (error) {
    toast.error('فشل تحميل الفاتورة')
  }
}

const shareWhatsApp = async () => {
  try {
    const response = await api.getWhatsAppMessage(route.params.id)
    const { message, phone } = response.data
    
    if (!phone) {
      toast.error('لا يوجد رقم هاتف للعميل')
      return
    }
    
    const cleanPhone = phone.replace(/[^0-9]/g, '')
    const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`
    window.open(whatsappUrl, '_blank')
    toast.success('تم فتح واتساب')
  } catch (error) {
    toast.error('فشل مشاركة الفاتورة')
  }
}

const voidInvoice = async () => {
  if (!confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟ لا يمكن التراجع عن هذا الإجراء.')) return

  try {
    await api.voidSale(route.params.id)
    toast.success('تم إلغاء الفاتورة')
    loadInvoice()
  } catch (error) {
    toast.error('فشل إلغاء الفاتورة')
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-GB')
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('en-GB', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
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

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'نقدي',
    card: 'بطاقة',
    bank: 'تحويل بنكي',
    wallet: 'محفظة إلكترونية',
    cod: 'الدفع عند الاستلام'
  }
  return labels[method] || method
}

onMounted(() => {
  loadInvoice()
  loadPayments()
})
</script>

<style scoped>
.invoice-detail {
  padding: 20px;
  direction: rtl;
}

.header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 30px;
}

.btn-back {
  padding: 8px;
  background: #f1f5f9;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.header h1 {
  flex: 1;
  font-size: 24px;
  color: #1e293b;
  margin: 0;
}

.status-badge {
  padding: 6px 16px;
  border-radius: 16px;
  font-size: 14px;
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

.content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card h2 {
  font-size: 18px;
  color: #1e293b;
  margin-bottom: 15px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.card-header h2 {
  margin: 0;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-item label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.info-item span {
  font-size: 14px;
  color: #1e293b;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th {
  background: #f8fafc;
  padding: 10px;
  text-align: right;
  font-weight: 600;
  color: #475569;
  border-bottom: 2px solid #e2e8f0;
}

td {
  padding: 10px;
  border-bottom: 1px solid #f1f5f9;
}

.totals {
  max-width: 400px;
  margin-right: auto;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

.total-row.total {
  font-weight: 600;
  font-size: 16px;
  color: #1e293b;
  border-top: 2px solid #e2e8f0;
  padding-top: 12px;
}

.total-row.paid {
  color: #059669;
}

.total-row.balance {
  color: #dc2626;
  font-weight: 600;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.actions-bar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-primary,
.btn-secondary,
.btn-success,
.btn-danger {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
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

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #64748b;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #e2e8f0;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
  font-weight: 500;
  color: #475569;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
}

.form-group small {
  display: block;
  margin-top: 5px;
  font-size: 12px;
  color: #64748b;
}
</style>
