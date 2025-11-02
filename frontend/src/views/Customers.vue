<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="البحث عن عميل..."
        class="input w-80"
      />
      <button @click="showAddModal = true" class="btn btn-primary flex items-center gap-2">
        <Plus :size="20" />
        إضافة عميل
      </button>
    </div>

    <div class="card overflow-hidden">
      <table class="table">
        <thead>
          <tr>
            <th>الاسم</th>
            <th>الهاتف</th>
            <th>الشريحة</th>
            <th>إجمالي المشتريات</th>
            <th>عدد الطلبات</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in filteredCustomers" :key="`customer-${customer.id}-${customer.segment}`">
            <td class="font-medium">{{ customer.name }}</td>
            <td class="font-mono">{{ toLatinNumbers(customer.phone) }}</td>
            <td>
              <span :class="[
                'badge',
                customer.segment === 'جملة' ? 'badge-success' :
                customer.segment === 'قطاعي' ? 'badge-primary' :
                'badge-warning'
              ]">
                {{ customer.segment || 'قطاعي' }}
              </span>
            </td>
            <td class="font-bold text-green-600">{{ formatCurrency(customer.total_purchases) }}</td>
            <td>{{ toLatinNumbers(customer.total_orders) }}</td>
            <td>
              <div class="flex items-center gap-2">
                <button @click="viewCustomer(customer.id)" class="text-gray-700 hover:text-gray-900">
                  <Eye :size="18" />
                </button>
                <button @click="editCustomer(customer)" class="text-blue-600 hover:text-blue-800">
                  <Edit :size="18" />
                </button>
                <button @click="deleteCustomer(customer.id)" class="text-red-600 hover:text-red-800">
                  <Trash2 :size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-6">{{ showEditModal ? 'تعديل عميل' : 'إضافة عميل جديد' }}</h3>
        
        <form @submit.prevent="submitCustomer" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">الاسم</label>
            <input v-model="customerForm.name" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الهاتف</label>
            <input v-model="customerForm.phone" type="tel" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">العنوان</label>
            <textarea v-model="customerForm.address" rows="3" class="input"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الشريحة</label>
            <select v-model="customerForm.segment" class="input" required>
              <option value="جملة">جملة</option>
              <option value="قطاعي">قطاعي</option>
              <option value="صفحة">صفحة</option>
            </select>
          </div>
          
          <div class="flex gap-3 justify-end mt-6">
            <button type="button" @click="closeModal" class="btn btn-secondary">إلغاء</button>
            <button type="submit" class="btn btn-primary">{{ showEditModal ? 'تحديث' : 'إضافة' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Plus, Edit, Trash2, Eye } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'

const toast = useToast()
const router = useRouter()
const customers = ref([])
const searchQuery = ref('')
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)

const customerForm = ref({
  name: '',
  phone: '',
  address: '',
  segment: 'قطاعي'
})

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  return customers.value.filter(c =>
    c.name.includes(searchQuery.value) ||
    c.phone.includes(searchQuery.value)
  )
})

const formatCurrency = (value) => formatCurrencyLatin(value)

const viewCustomer = (id) => {
  router.push(`/clients/${id}`)
}

const fetchCustomers = async () => {
  try {
    const response = await api.getCustomers({ per_page: 100 })
    customers.value = response.data.data || response.data
  } catch (error) {
    toast.error('فشل تحميل العملاء')
    console.error(error)
  }
}

const submitCustomer = async () => {
  try {
    // Only send allowed fields
    const payload = {
      name: customerForm.value.name,
      phone: customerForm.value.phone,
      address: customerForm.value.address,
      segment: customerForm.value.segment
    }
    
    console.log('Submitting customer form:', payload)
    if (showEditModal.value) {
      console.log('Updating customer ID:', editingId.value)
      const response = await api.updateCustomer(editingId.value, payload)
      console.log('Update response:', response.data)
      
      // Update the customer in the list immediately
      const index = customers.value.findIndex(c => c.id === editingId.value)
      if (index !== -1) {
        customers.value[index] = { ...customers.value[index], ...response.data }
      }
      
      toast.success('تم تحديث العميل بنجاح')
    } else {
      const response = await api.createCustomer(payload)
      console.log('Create response:', response.data)
      toast.success('تم إضافة العميل بنجاح')
    }
    closeModal()
    await fetchCustomers()
  } catch (error) {
    console.error('Submit error:', error)
    console.error('Error response:', error.response?.data)
    toast.error('حدث خطأ')
  }
}

const editCustomer = (customer) => {
  customerForm.value = { ...customer }
  editingId.value = customer.id
  showEditModal.value = true
}

const deleteCustomer = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا العميل؟')) return
  
  try {
    await api.deleteCustomer(id)
    toast.success('تم حذف العميل بنجاح')
    fetchCustomers()
  } catch (error) {
    toast.error('فشل حذف العميل')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingId.value = null
  customerForm.value = {
    name: '',
    phone: '',
    address: '',
    segment: 'قطاعي'
  }
}

onMounted(() => {
  fetchCustomers()
})
</script>
