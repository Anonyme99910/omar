<template>
  <div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">المصروفات</h1>
        <p class="text-gray-600 mt-1">إدارة وتتبع جميع المصروفات</p>
      </div>
      <div class="flex gap-3">
        <button @click="showTypeModal = true" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
          <Plus :size="20" />
          إضافة نوع مصروف
        </button>
        <button @click="openAddExpenseModal" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
          <Plus :size="20" />
          إضافة مصروف
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm mb-1">إجمالي المصروفات</p>
            <h3 class="text-3xl font-bold">{{ formatCurrency(statistics.total_expenses) }}</h3>
            <p class="text-blue-100 text-xs mt-1">{{ periodLabel }}</p>
          </div>
          <Receipt :size="48" class="text-blue-200" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm mb-1">المصروفات الشهرية</p>
            <h3 class="text-3xl font-bold">{{ formatCurrency(statistics.monthly_expenses) }}</h3>
            <p class="text-green-100 text-xs mt-1">متكررة شهرياً</p>
          </div>
          <Calendar :size="48" class="text-green-200" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm mb-1">المصروفات السنوية</p>
            <h3 class="text-3xl font-bold">{{ formatCurrency(statistics.yearly_expenses) }}</h3>
            <p class="text-purple-100 text-xs mt-1">متكررة سنوياً</p>
          </div>
          <TrendingUp :size="48" class="text-purple-200" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm mb-1">المصروفات الثابتة</p>
            <h3 class="text-3xl font-bold">{{ formatCurrency(statistics.fixed_expenses) }}</h3>
            <p class="text-orange-100 text-xs mt-1">مصروفات ثابتة</p>
          </div>
          <Lock :size="48" class="text-orange-200" />
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium mb-2">من تاريخ</label>
          <input v-model="filters.start_date" type="date" class="input" @change="fetchExpenses" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">إلى تاريخ</label>
          <input v-model="filters.end_date" type="date" class="input" @change="fetchExpenses" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">نوع المصروف</label>
          <select v-model="filters.expense_type_id" class="input" @change="fetchExpenses">
            <option value="">الكل</option>
            <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
              {{ type.name_ar }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">التكرار</label>
          <select v-model="filters.recurrence_type" class="input" @change="fetchExpenses">
            <option value="">الكل</option>
            <option value="once">مرة واحدة</option>
            <option value="monthly">شهري</option>
            <option value="yearly">سنوي</option>
          </select>
        </div>
      </div>
      <div class="flex gap-3 mt-4">
        <button @click="exportPDF" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
          <Download :size="20" />
          تصدير PDF
        </button>
        <button @click="resetFilters" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
          <X :size="20" />
          إعادة تعيين
        </button>
      </div>
    </div>

    <!-- Expenses Table -->
    <div class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع المصروف</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التكرار</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ملاحظات</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="expense in expenses" :key="expense.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDateLatin(expense.expense_date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium">{{ expense.expense_type?.name_ar || 'غير محدد' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-lg font-bold text-red-600">{{ formatCurrency(expense.amount) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getRecurrenceBadgeClass(expense.recurrence_type)">
                  {{ getRecurrenceLabel(expense.recurrence_type) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                {{ expense.remarks || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex gap-2">
                  <button @click="editExpense(expense)" class="text-blue-600 hover:text-blue-800">
                    <Edit2 :size="18" />
                  </button>
                  <button @click="deleteExpenseItem(expense.id)" class="text-red-600 hover:text-red-800">
                    <Trash2 :size="18" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="expenses.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                لا توجد مصروفات
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Expense Modal -->
    <div v-if="showExpenseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeExpenseModal">
      <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-bold mb-4">{{ isEditing ? 'تعديل مصروف' : 'إضافة مصروف جديد' }}</h3>
        <form @submit.prevent="submitExpense" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">نوع المصروف *</label>
              <select v-model="expenseForm.expense_type_id" required class="input">
                <option value="">اختر نوع المصروف</option>
                <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                  {{ type.name_ar }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">المبلغ *</label>
              <input v-model="expenseForm.amount" type="number" step="0.01" required class="input" placeholder="0.00" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">التاريخ *</label>
              <input v-model="expenseForm.expense_date" type="date" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">التكرار *</label>
              <select v-model="expenseForm.recurrence_type" required class="input">
                <option value="once">مرة واحدة</option>
                <option value="monthly">شهري</option>
                <option value="yearly">سنوي</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">ملاحظات</label>
            <textarea v-model="expenseForm.remarks" rows="3" class="input" placeholder="أضف ملاحظات..."></textarea>
          </div>
          <div class="flex items-center">
            <input v-model="expenseForm.is_fixed" type="checkbox" id="is_fixed" class="mr-2" />
            <label for="is_fixed" class="text-sm font-medium">مصروف ثابت</label>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="closeExpenseModal" class="btn btn-secondary flex-1">إلغاء</button>
            <button type="submit" class="btn btn-primary flex-1">{{ isEditing ? 'تحديث' : 'حفظ' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add Expense Type Modal -->
    <div v-if="showTypeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showTypeModal = false">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4">إضافة نوع مصروف</h3>
        <form @submit.prevent="submitExpenseType" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالعربية *</label>
            <input v-model="typeForm.name_ar" type="text" required class="input" placeholder="مثال: الإيجار" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالإنجليزية</label>
            <input v-model="typeForm.name_en" type="text" class="input" placeholder="Example: Rent" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الوصف</label>
            <textarea v-model="typeForm.description" rows="2" class="input" placeholder="وصف نوع المصروف..."></textarea>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="showTypeModal = false" class="btn btn-secondary flex-1">إلغاء</button>
            <button type="submit" class="btn btn-primary flex-1">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Plus, Receipt, Calendar, TrendingUp, Lock, Download, X, Edit2, Trash2 } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()

const expenses = ref([])
const expenseTypes = ref([])
const statistics = ref({
  total_expenses: 0,
  monthly_expenses: 0,
  yearly_expenses: 0,
  fixed_expenses: 0
})

const showExpenseModal = ref(false)
const showTypeModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const filters = ref({
  start_date: '2025-01-01',
  end_date: '2025-12-31',
  expense_type_id: '',
  recurrence_type: ''
})

const expenseForm = ref({
  expense_type_id: '',
  amount: '',
  expense_date: new Date().toISOString().split('T')[0],
  recurrence_type: 'once',
  remarks: '',
  is_fixed: false
})

const typeForm = ref({
  name_ar: '',
  name_en: '',
  description: ''
})

const periodLabel = computed(() => {
  const start = new Date(filters.value.start_date).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
  const end = new Date(filters.value.end_date).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
  return `${start} - ${end}`
})

const fetchExpenses = async () => {
  try {
    const params = {}
    if (filters.value.start_date) params.start_date = filters.value.start_date
    if (filters.value.end_date) params.end_date = filters.value.end_date
    if (filters.value.expense_type_id) params.expense_type_id = filters.value.expense_type_id
    if (filters.value.recurrence_type) params.recurrence_type = filters.value.recurrence_type
    
    console.log('Fetching expenses with params:', params)
    const response = await api.getExpenses(params)
    console.log('API Response:', response.data)
    expenses.value = response.data.data || response.data || []
    console.log('Expenses loaded:', expenses.value.length, expenses.value)
  } catch (error) {
    console.error('Failed to fetch expenses:', error)
    console.error('Error details:', error.response?.data)
    toast.error('فشل تحميل المصروفات')
  }
}

const fetchExpenseTypes = async () => {
  try {
    const response = await api.getExpenseTypes()
    expenseTypes.value = response.data.data || []
  } catch (error) {
    console.error('Failed to fetch expense types:', error)
  }
}

const fetchStatistics = async () => {
  try {
    const params = {}
    if (filters.value.start_date) params.start_date = filters.value.start_date
    if (filters.value.end_date) params.end_date = filters.value.end_date
    
    const response = await api.getExpenseStatistics(params)
    statistics.value = response.data.data || {
      total_expenses: 0,
      monthly_expenses: 0,
      yearly_expenses: 0,
      fixed_expenses: 0
    }
    console.log('Statistics:', statistics.value)
  } catch (error) {
    console.error('Failed to fetch statistics:', error)
  }
}

const openAddExpenseModal = () => {
  isEditing.value = false
  editingId.value = null
  expenseForm.value = {
    expense_type_id: '',
    amount: '',
    expense_date: new Date().toISOString().split('T')[0],
    recurrence_type: 'once',
    remarks: '',
    is_fixed: false
  }
  showExpenseModal.value = true
}

const editExpense = (expense) => {
  isEditing.value = true
  editingId.value = expense.id
  expenseForm.value = {
    expense_type_id: expense.expense_type_id,
    amount: expense.amount,
    expense_date: expense.expense_date,
    recurrence_type: expense.recurrence_type,
    remarks: expense.remarks || '',
    is_fixed: expense.is_fixed
  }
  showExpenseModal.value = true
}

const submitExpense = async () => {
  try {
    if (isEditing.value) {
      await api.updateExpense(editingId.value, expenseForm.value)
      toast.success('تم تحديث المصروف بنجاح')
    } else {
      await api.createExpense(expenseForm.value)
      toast.success('تم إضافة المصروف بنجاح')
    }
    closeExpenseModal()
    fetchExpenses()
    fetchStatistics()
  } catch (error) {
    console.error('Failed to save expense:', error)
    toast.error('فشل حفظ المصروف')
  }
}

const deleteExpenseItem = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا المصروف؟')) return
  
  try {
    await api.deleteExpense(id)
    toast.success('تم حذف المصروف بنجاح')
    fetchExpenses()
    fetchStatistics()
  } catch (error) {
    console.error('Failed to delete expense:', error)
    toast.error('فشل حذف المصروف')
  }
}

const submitExpenseType = async () => {
  try {
    await api.createExpenseType(typeForm.value)
    toast.success('تم إضافة نوع المصروف بنجاح')
    showTypeModal.value = false
    typeForm.value = { name_ar: '', name_en: '', description: '' }
    fetchExpenseTypes()
  } catch (error) {
    console.error('Failed to save expense type:', error)
    toast.error('فشل حفظ نوع المصروف')
  }
}

const closeExpenseModal = () => {
  showExpenseModal.value = false
  isEditing.value = false
  editingId.value = null
}

const resetFilters = () => {
  filters.value = {
    start_date: '2025-01-01',
    end_date: '2025-12-31',
    expense_type_id: '',
    recurrence_type: ''
  }
  fetchExpenses()
  fetchStatistics()
}

const exportPDF = async () => {
  try {
    toast.info('جاري إنشاء التقرير...')
    
    // Create a temporary container for PDF content
    const pdfContainer = document.createElement('div')
    pdfContainer.style.position = 'absolute'
    pdfContainer.style.left = '-9999px'
    pdfContainer.style.width = '210mm' // A4 width
    pdfContainer.style.padding = '20mm'
    pdfContainer.style.backgroundColor = 'white'
    pdfContainer.style.fontFamily = 'Cairo, Arial, sans-serif'
    pdfContainer.style.direction = 'rtl'
    
    // Build HTML content
    pdfContainer.innerHTML = `
      <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #1e40af; font-size: 28px; margin-bottom: 10px;">تقرير المصروفات</h1>
        <p style="color: #666; font-size: 14px;">الفترة: ${filters.value.start_date} إلى ${filters.value.end_date}</p>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 20px; border-radius: 10px;">
          <div style="font-size: 14px; opacity: 0.9;">إجمالي المصروفات</div>
          <div style="font-size: 24px; font-weight: bold; margin-top: 5px;">${formatCurrency(statistics.value.total_expenses)}</div>
        </div>
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 10px;">
          <div style="font-size: 14px; opacity: 0.9;">المصروفات الشهرية</div>
          <div style="font-size: 24px; font-weight: bold; margin-top: 5px;">${formatCurrency(statistics.value.monthly_expenses)}</div>
        </div>
        <div style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); color: white; padding: 20px; border-radius: 10px;">
          <div style="font-size: 14px; opacity: 0.9;">المصروفات السنوية</div>
          <div style="font-size: 24px; font-weight: bold; margin-top: 5px;">${formatCurrency(statistics.value.yearly_expenses)}</div>
        </div>
        <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; padding: 20px; border-radius: 10px;">
          <div style="font-size: 14px; opacity: 0.9;">المصروفات الثابتة</div>
          <div style="font-size: 24px; font-weight: bold; margin-top: 5px;">${formatCurrency(statistics.value.fixed_expenses)}</div>
        </div>
      </div>
      
      <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
          <tr style="background-color: #3b82f6; color: white;">
            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">التاريخ</th>
            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">نوع المصروف</th>
            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">المبلغ</th>
            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">التكرار</th>
            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          ${expenses.value.map((exp, index) => `
            <tr style="background-color: ${index % 2 === 0 ? '#f9fafb' : 'white'};">
              <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">${formatDateLatin(exp.expense_date)}</td>
              <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">${exp.expense_type?.name_ar || '-'}</td>
              <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; color: #dc2626;">${formatCurrency(exp.amount)}</td>
              <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">${getRecurrenceLabel(exp.recurrence_type)}</td>
              <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">${exp.remarks || '-'}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
      
      <div style="margin-top: 30px; text-align: center; color: #666; font-size: 12px;">
        <p>تم إنشاء التقرير في ${new Date().toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
      </div>
    `
    
    document.body.appendChild(pdfContainer)
    
    // Use html2canvas to capture the content
    const canvas = await window.html2canvas(pdfContainer, {
      scale: 2,
      useCORS: true,
      logging: false,
      backgroundColor: '#ffffff'
    })
    
    // Remove temporary container
    document.body.removeChild(pdfContainer)
    
    // Create PDF
    const { jsPDF } = window.jspdf
    const imgWidth = 210 // A4 width in mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width
    const imgData = canvas.toDataURL('image/png')
    
    const pdf = new jsPDF('p', 'mm', 'a4')
    let heightLeft = imgHeight
    let position = 0
    
    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight)
    heightLeft -= 297 // A4 height
    
    // Add new pages if content is longer than one page
    while (heightLeft > 0) {
      position = heightLeft - imgHeight
      pdf.addPage()
      pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight)
      heightLeft -= 297
    }
    
    pdf.save(`expenses-${new Date().toISOString().split('T')[0]}.pdf`)
    toast.success('تم تصدير التقرير بنجاح')
  } catch (error) {
    console.error('PDF export error:', error)
    toast.error('فشل تصدير التقرير')
  }
}

const toLatinNumbers = (str) => {
  if (typeof str !== 'string') str = String(str)
  const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩']
  return str.replace(/[٠-٩]/g, (d) => arabicNumbers.indexOf(d))
}

const formatCurrency = (value) => {
  const formatted = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0)
  return formatted.replace('EGP', 'ج.م')
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateLatin = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const getRecurrenceLabel = (type) => {
  const labels = {
    once: 'مرة واحدة',
    monthly: 'شهري',
    yearly: 'سنوي'
  }
  return labels[type] || type
}

const getRecurrenceBadgeClass = (type) => {
  const classes = {
    once: 'badge badge-secondary',
    monthly: 'badge badge-primary',
    yearly: 'badge badge-success'
  }
  return classes[type] || 'badge'
}

onMounted(() => {
  fetchExpenses()
  fetchExpenseTypes()
  fetchStatistics()
})
</script>
