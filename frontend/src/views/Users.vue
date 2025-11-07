<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">إدارة المستخدمين</h2>
      <button
        @click="showModal = true"
        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2"
      >
        <UserPlus :size="20" />
        <span>إضافة مستخدم</span>
      </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
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
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ user.is_active ? 'نشط' : 'غير نشط' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button
                @click="editUser(user)"
                class="text-primary-600 hover:text-primary-900 ml-3"
              >
                <Edit2 :size="18" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit User Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">{{ editingUser ? 'تعديل مستخدم' : 'إضافة مستخدم جديد' }}</h3>
        
        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>

          <div v-if="!editingUser">
            <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الدور</label>
            <select
              v-model="form.role"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            >
              <option value="admin">مدير</option>
              <option value="manager">مشرف</option>
              <option value="cashier">كاشير</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            <label for="is_active" class="text-sm font-medium text-gray-700">حساب نشط</label>
          </div>

          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
            >
              حفظ
            </button>
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
            >
              إلغاء
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UserPlus, Edit2 } from 'lucide-vue-next'
import { useToast } from 'vue-toastification'

const toast = useToast()

const users = ref([
  {
    id: 1,
    name: 'Admin',
    email: 'admin@perfume.com',
    role: 'admin',
    is_active: true
  }
])

const showModal = ref(false)
const editingUser = ref(null)
const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'cashier',
  is_active: true
})

const getRoleLabel = (role) => {
  const labels = {
    admin: 'مدير',
    manager: 'مشرف',
    cashier: 'كاشير'
  }
  return labels[role] || role
}

const editUser = (user) => {
  editingUser.value = user
  form.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    is_active: user.is_active
  }
  showModal.value = true
}

const saveUser = () => {
  if (editingUser.value) {
    // Update existing user
    const index = users.value.findIndex(u => u.id === editingUser.value.id)
    if (index !== -1) {
      users.value[index] = {
        ...users.value[index],
        ...form.value
      }
    }
    toast.success('تم تحديث المستخدم بنجاح')
  } else {
    // Add new user
    users.value.push({
      id: Date.now(),
      ...form.value
    })
    toast.success('تم إضافة المستخدم بنجاح')
  }
  closeModal()
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
  form.value = {
    name: '',
    email: '',
    password: '',
    role: 'cashier',
    is_active: true
  }
}
</script>
