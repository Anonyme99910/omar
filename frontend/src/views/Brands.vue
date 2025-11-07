<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">العلامات التجارية</h2>
      <button @click="showAddModal = true" class="btn btn-primary flex items-center gap-2">
        <Plus :size="20" />
        إضافة علامة تجارية
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="brand in brands" :key="brand.id" class="card">
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-900">{{ brand.name_ar }}</h3>
            <p class="text-sm text-gray-500">{{ brand.name }}</p>
          </div>
          <span :class="brand.is_active ? 'badge-success' : 'badge-danger'" class="badge">
            {{ brand.is_active ? 'نشط' : 'غير نشط' }}
          </span>
        </div>
        
        <p class="text-gray-600 text-sm mb-4">{{ brand.description || 'لا يوجد وصف' }}</p>
        
        <div class="flex items-center justify-between pt-4 border-t">
          <span class="text-sm text-gray-500">{{ brand.products_count || 0 }} منتج</span>
          <div class="flex gap-2">
            <button @click="editBrand(brand)" class="text-blue-600 hover:text-blue-800">
              <Edit :size="18" />
            </button>
            <button @click="deleteBrand(brand.id)" class="text-red-600 hover:text-red-800">
              <Trash2 :size="18" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-6">{{ showEditModal ? 'تعديل علامة تجارية' : 'إضافة علامة تجارية جديدة' }}</h3>
        
        <form @submit.prevent="submitBrand" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالعربية</label>
            <input v-model="brandForm.name_ar" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الاسم بالإنجليزية</label>
            <input v-model="brandForm.name" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الوصف</label>
            <textarea v-model="brandForm.description" rows="3" class="input"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">الحالة</label>
            <select v-model="brandForm.is_active" class="input">
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
const brands = ref([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)

const brandForm = ref({
  name: '',
  name_ar: '',
  description: '',
  is_active: true
})

const fetchBrands = async () => {
  try {
    const response = await api.getBrands()
    brands.value = response.data
  } catch (error) {
    toast.error('فشل تحميل العلامات التجارية')
  }
}

const submitBrand = async () => {
  try {
    if (showEditModal.value) {
      await api.updateBrand(editingId.value, brandForm.value)
      toast.success('تم تحديث العلامة التجارية بنجاح')
    } else {
      await api.createBrand(brandForm.value)
      toast.success('تم إضافة العلامة التجارية بنجاح')
    }
    closeModal()
    fetchBrands()
  } catch (error) {
    toast.error('حدث خطأ')
  }
}

const editBrand = (brand) => {
  brandForm.value = { ...brand }
  editingId.value = brand.id
  showEditModal.value = true
}

const deleteBrand = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذه العلامة التجارية؟')) return
  
  try {
    await api.deleteBrand(id)
    toast.success('تم حذف العلامة التجارية بنجاح')
    fetchBrands()
  } catch (error) {
    toast.error('فشل حذف العلامة التجارية')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingId.value = null
  brandForm.value = {
    name: '',
    name_ar: '',
    description: '',
    is_active: true
  }
}

onMounted(() => {
  fetchBrands()
})
</script>
