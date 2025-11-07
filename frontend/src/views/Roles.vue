<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">الأدوار والصلاحيات</h2>
        <p class="text-gray-500 mt-1">إدارة أدوار المستخدمين وصلاحياتهم</p>
      </div>
    </div>

    <!-- Employees Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <div
        v-for="employee in employees"
        :key="employee.id"
        class="card hover:shadow-lg transition-shadow relative"
      >
        <!-- Permission Icon Button -->
        <button
          @click="openPermissionsModal(employee)"
          class="absolute top-4 left-4 w-10 h-10 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition-colors"
          title="إدارة الصلاحيات"
        >
          <Settings :size="20" class="text-blue-600" />
        </button>

        <div class="flex flex-col items-center text-center pt-2">
          <!-- User Avatar -->
          <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-3">
            <User :size="32" class="text-white" />
          </div>
          
          <!-- Employee Info -->
          <h3 class="text-xl font-bold text-gray-900 mb-1">{{ employee.name }}</h3>
          <p class="text-sm text-gray-500 mb-3">{{ getRoleLabel(employee.role) }}</p>
          
          <!-- Permissions Summary -->
          <div class="w-full space-y-2">
            <p class="text-xs font-medium text-gray-700">الصلاحيات:</p>
            <div class="flex flex-wrap gap-1 justify-center">
              <span 
                v-for="permission in getEmployeePermissions(employee)"
                :key="permission"
                class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs"
              >
                {{ translatePermission(permission) }}
              </span>
              <span v-if="getEmployeePermissions(employee).length === 0" class="text-xs text-gray-400">
                لا توجد صلاحيات
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Permissions Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold text-gray-900">إدارة صلاحيات {{ selectedEmployee?.name }}</h3>
              <p class="text-sm text-gray-500 mt-1">{{ getRoleLabel(selectedEmployee?.role) }}</p>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <X :size="24" />
            </button>
          </div>

          <!-- Permissions List -->
          <div class="space-y-4">
            <div v-for="permission in availablePermissions" :key="permission.key" class="border rounded-lg p-4 hover:bg-gray-50">
              <label class="flex items-start cursor-pointer">
                <input
                  type="checkbox"
                  v-model="selectedPermissions"
                  :value="permission.key"
                  class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                />
                <div class="mr-3 flex-1">
                  <div class="flex items-center gap-2">
                    <component :is="permission.icon" :size="20" :class="permission.color" />
                    <span class="font-medium text-gray-900">{{ permission.label }}</span>
                  </div>
                  <p class="text-sm text-gray-500 mt-1">{{ permission.description }}</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Modal Actions -->
          <div class="flex gap-3 mt-6 pt-6 border-t">
            <button
              @click="savePermissions"
              class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
            >
              حفظ الصلاحيات
            </button>
            <button
              @click="closeModal"
              class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
            >
              إلغاء
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { User, Settings, X, ShoppingCart, Users, Package, FileText, TrendingUp, DollarSign, Receipt, Warehouse } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()
const employees = ref([])
const showModal = ref(false)
const selectedEmployee = ref(null)
const selectedPermissions = ref([])

const availablePermissions = [
  {
    key: 'dashboard',
    label: 'لوحة التحكم',
    description: 'عرض لوحة التحكم والإحصائيات',
    icon: TrendingUp,
    color: 'text-blue-600'
  },
  {
    key: 'clients',
    label: 'العملاء',
    description: 'إدارة بيانات العملاء',
    icon: Users,
    color: 'text-indigo-600'
  },
  {
    key: 'employees',
    label: 'الموظفون',
    description: 'إدارة الموظفين',
    icon: Users,
    color: 'text-purple-600'
  },
  {
    key: 'roles',
    label: 'الأدوار والصلاحيات',
    description: 'إدارة أدوار المستخدمين',
    icon: FileText,
    color: 'text-orange-600'
  },
  {
    key: 'pos',
    label: 'نقطة البيع',
    description: 'إنشاء فواتير البيع والمبيعات',
    icon: ShoppingCart,
    color: 'text-green-600'
  },
  {
    key: 'invoices',
    label: 'الفواتير',
    description: 'عرض وإدارة الفواتير',
    icon: Receipt,
    color: 'text-purple-600'
  },
  {
    key: 'sales-analysis',
    label: 'تحليل المبيعات',
    description: 'عرض تحليلات المبيعات',
    icon: TrendingUp,
    color: 'text-blue-600'
  },
  {
    key: 'expenses',
    label: 'المصروفات',
    description: 'إدارة المصروفات والنفقات',
    icon: DollarSign,
    color: 'text-red-600'
  },
  {
    key: 'stock',
    label: 'المخزون',
    description: 'إدارة المخزون',
    icon: Package,
    color: 'text-teal-600'
  },
  {
    key: 'inventory',
    label: 'المنتجات التالفة',
    description: 'إدارة المنتجات التالفة',
    icon: Warehouse,
    color: 'text-gray-600'
  }
]

const getRoleLabel = (role) => {
  const labels = {
    'admin': 'مدير',
    'manager': 'مدير فرع',
    'cashier': 'كاشير',
    'inventory': 'مخزن'
  }
  return labels[role] || role
}

const translatePermission = (permission) => {
  const perm = availablePermissions.find(p => p.key === permission)
  return perm ? perm.label : permission
}

const getEmployeePermissions = (employee) => {
  return employee.permissions || []
}

const openPermissionsModal = (employee) => {
  selectedEmployee.value = employee
  selectedPermissions.value = [...(employee.permissions || [])]
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedEmployee.value = null
  selectedPermissions.value = []
}

const savePermissions = async () => {
  try {
    console.log('Saving permissions:', selectedPermissions.value)
    
    const response = await api.updateEmployeePermissions(selectedEmployee.value.id, {
      permissions: selectedPermissions.value
    })
    
    console.log('Save response:', response)
    
    // Update local data
    const employee = employees.value.find(e => e.id === selectedEmployee.value.id)
    if (employee) {
      employee.permissions = [...selectedPermissions.value]
    }
    
    toast.success('تم تحديث الصلاحيات بنجاح')
    closeModal()
  } catch (error) {
    console.error('Failed to update permissions:', error)
    console.error('Error response:', error.response?.data)
    
    const errorMsg = error.response?.data?.message || error.response?.data?.errors || 'فشل تحديث الصلاحيات'
    toast.error(typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg))
  }
}

const fetchEmployees = async () => {
  try {
    const res = await api.getEmployees()
    employees.value = res.data || []
  } catch (error) {
    console.error('Failed to load employees:', error)
    toast.error('فشل تحميل الموظفين')
    employees.value = []
  }
}

onMounted(() => {
  fetchEmployees()
})
</script>
