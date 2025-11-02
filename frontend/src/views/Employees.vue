<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">إدارة الموظفين</h2>
      <button
        @click="openCreate"
        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2"
      >
        <UserPlus :size="20" />
        <span>إضافة موظف</span>
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الدور</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="u in employees" :key="u.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ u.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ u.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ roleLabel(u.role) }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="['px-2 py-1 text-xs font-semibold rounded-full', u.is_active ? 'bg-green-100 text-green-800':'bg-red-100 text-red-800']">{{ u.is_active ? 'نشط' : 'غير نشط' }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openEdit(u)" class="text-primary-600 hover:text-primary-900 ml-3"><Edit2 :size="18" /></button>
              <button @click="remove(u)" class="text-red-600 hover:text-red-800"><Trash2 :size="18" /></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 grid place-items-center z-50" @click.self="close">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">{{ editing ? 'تعديل موظف' : 'إضافة موظف' }}</h3>
        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم</label>
            <input v-model="form.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
            <input type="email" v-model="form.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required />
          </div>
          <div v-if="!editing">
            <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
            <input type="password" v-model="form.password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الدور</label>
            <select v-model="form.role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" required>
              <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name_ar }}</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <input id="active" type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
            <label for="active" class="text-sm font-medium text-gray-700">حساب نشط</label>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">حفظ</button>
            <button type="button" @click="close" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UserPlus, Edit2, Trash2 } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()
const employees = ref([])
const roles = ref([])

const show = ref(false)
const editing = ref(false)
const currentId = ref(null)
const form = ref({ name: '', email: '', password: '', role: 'cashier', is_active: true })

const roleLabel = (key) => {
  const labels = {
    'admin': 'مدير',
    'manager': 'مدير فرع',
    'cashier': 'كاشير',
    'inventory': 'مخزن'
  }
  return labels[key] || key
}

const fetchAll = async () => {
  const [empRes, roleRes] = await Promise.all([
    api.getEmployees(),
    api.getRoles()
  ])
  employees.value = empRes.data.data || empRes.data // supports paginate or array
  roles.value = roleRes.data
}

const openCreate = () => {
  editing.value = false
  currentId.value = null
  form.value = { name: '', email: '', password: '', role: 'cashier', is_active: true }
  show.value = true
}
const openEdit = (u) => {
  editing.value = true
  currentId.value = u.id
  form.value = { name: u.name, email: u.email, password: '', role: u.role, is_active: !!u.is_active }
  show.value = true
}
const close = () => { show.value = false }

const save = async () => {
  try {
    if (editing.value) {
      const payload = { name: form.value.name, email: form.value.email, role: form.value.role, is_active: form.value.is_active }
      await api.updateEmployee(currentId.value, payload)
      toast.success('تم تحديث الموظف بنجاح')
    } else {
      await api.createEmployee(form.value)
      toast.success('تم إضافة الموظف بنجاح')
    }
    show.value = false
    fetchAll()
  } catch (e) {
    toast.error('حدث خطأ أثناء الحفظ')
  }
}

const remove = async (u) => {
  if (!confirm('هل أنت متأكد من الحذف؟')) return
  await api.deleteEmployee(u.id)
  toast.success('تم الحذف')
  fetchAll()
}

onMounted(fetchAll)
</script>
