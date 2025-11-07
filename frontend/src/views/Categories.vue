<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">التصنيفات</h2>
      <button @click="showAddModal = true" class="btn btn-primary flex items-center gap-2">
        <Plus :size="20" />
        إضافة تصنيف
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="category in categories" :key="category.id" class="card">
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900">{{ category.name_ar }}</h3>
            <p class="text-sm text-gray-500">{{ category.name }}</p>
          </div>
          <span :class="category.is_active ? 'badge-success' : 'badge-danger'" class="badge">
            {{ category.is_active ? 'نشط' : 'غير نشط' }}
          </span>
        </div>
        
        <p class="text-gray-600 text-sm mb-4">{{ category.description || 'لا يوجد وصف' }}</p>
        
        <div class="flex items-center justify-between pt-4 border-t">
          <span class="text-sm text-gray-500">{{ category.products_count || 0 }} منتج</span>
          <div class="flex gap-2">
            <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-800">
              <Edit :size="18" />
            </button>
            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-800">
              <Trash2 :size="18" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-6">{{ showEditModal ? 'تعديل تصنيف' : 'إضافة تصنيف جديد' }}</h3>
        
        <form @submit.prevent="submitCategory" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالعربية</label>
            <input v-model="categoryForm.name_ar" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالإنجليزية</label>
            <input v-model="categoryForm.name" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الوصف</label>
            <textarea v-model="categoryForm.description" rows="3" class="input"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الحالة</label>
            <select v-model="categoryForm.is_active" class="input">
              <option :value="true">نشط</option>
              <option :value="false">غير نشط</option>
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
import { ref, onMounted } from 'vue'
import { Plus, Edit, Trash2 } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()
const categories = ref([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)

const categoryForm = ref({
  name: '',
  name_ar: '',
  description: '',
  is_active: true
})

const fetchCategories = async () => {
  try {
    const response = await api.getCategories()
    categories.value = response.data
  } catch (error) {
    toast.error('فشل تحميل التصنيفات')
  }
}

const submitCategory = async () => {
  try {
    if (showEditModal.value) {
      await api.updateCategory(editingId.value, categoryForm.value)
      toast.success('تم تحديث التصنيف بنجاح')
    } else {
      await api.createCategory(categoryForm.value)
      toast.success('تم إضافة التصنيف بنجاح')
    }
    closeModal()
    fetchCategories()
  } catch (error) {
    toast.error('حدث خطأ')
  }
}

const editCategory = (category) => {
  categoryForm.value = { ...category }
  editingId.value = category.id
  showEditModal.value = true
}

const deleteCategory = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا التصنيف؟')) return
  
  try {
    await api.deleteCategory(id)
    toast.success('تم حذف التصنيف بنجاح')
    fetchCategories()
  } catch (error) {
    toast.error('فشل حذف التصنيف')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingId.value = null
  categoryForm.value = {
    name: '',
    name_ar: '',
    description: '',
    is_active: true
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
