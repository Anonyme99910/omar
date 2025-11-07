<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="البحث عن منتج..."
          class="input w-80"
        />
        <select v-model="filterCategory" class="input w-48">
          <option value="">كل التصنيفات</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name_ar }}
          </option>
        </select>
        <select v-model="filterBrand" class="input w-48">
          <option value="">كل العلامات</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">
            {{ brand.name_ar }}
          </option>
        </select>
      </div>
      <button @click="showAddModal = true" class="btn btn-primary flex items-center gap-2">
        <Plus :size="20" />
        إضافة منتج
      </button>
    </div>

    <!-- Products Table -->
    <div class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>الباركود</th>
              <th>الاسم</th>
              <th>التصنيف</th>
              <th>العلامة التجارية</th>
              <th>السعر</th>
              <th>المخزون</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
              <td class="font-mono">{{ toLatinNumbers(product.barcode) }}</td>
              <td>
                <div>
                  <p class="font-medium">{{ product.name_ar }}</p>
                  <p class="text-xs text-gray-500">{{ product.size }}</p>
                </div>
              </td>
              <td>{{ product.category?.name_ar }}</td>
              <td>{{ product.brand?.name_ar }}</td>
              <td class="font-bold text-green-600">{{ formatCurrencyLatin(product.selling_price) }}</td>
              <td>
                <span
                  :class="{
                    'badge-danger': product.stock_quantity === 0,
                    'badge-warning': product.is_low_stock && product.stock_quantity > 0,
                    'badge-success': !product.is_low_stock && product.stock_quantity > 0
                  }"
                  class="badge"
                >
                  {{ toLatinNumbers(product.stock_quantity) }} قطعة
                </span>
              </td>
              <td>
                <span :class="product.is_active ? 'badge-success' : 'badge-danger'" class="badge">
                  {{ product.is_active ? 'نشط' : 'غير نشط' }}
                </span>
              </td>
              <td>
                <div class="flex items-center gap-2">
                  <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-800">
                    <Edit :size="18" />
                  </button>
                  <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-800">
                    <Trash2 :size="18" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-bold mb-6">{{ showEditModal ? 'تعديل منتج' : 'إضافة منتج جديد' }}</h3>
        
        <form @submit.prevent="submitProduct" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">الاسم بالعربية</label>
              <input v-model="productForm.name_ar" type="text" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الاسم بالإنجليزية</label>
              <input v-model="productForm.name" type="text" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الباركود</label>
              <input v-model="productForm.barcode" type="text" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">SKU</label>
              <input v-model="productForm.sku" type="text" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">التصنيف</label>
              <select v-model="productForm.category_id" required class="input">
                <option value="">اختر التصنيف</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name_ar }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">العلامة التجارية</label>
              <select v-model="productForm.brand_id" required class="input">
                <option value="">اختر العلامة</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.name_ar }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">سعر التكلفة</label>
              <input v-model.number="productForm.cost_price" type="number" step="0.01" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">سعر البيع</label>
              <input v-model.number="productForm.selling_price" type="number" step="0.01" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الكمية</label>
              <input v-model.number="productForm.stock_quantity" type="number" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الحد الأدنى</label>
              <input v-model.number="productForm.min_stock_level" type="number" required class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الحجم</label>
              <input v-model="productForm.size" type="text" class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">الحالة</label>
              <select v-model="productForm.is_active" class="input">
                <option :value="true">نشط</option>
                <option :value="false">غير نشط</option>
              </select>
            </div>
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
import { Plus, Edit, Trash2 } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'

const toast = useToast()

const products = ref([])
const categories = ref([])
const brands = ref([])
const searchQuery = ref('')
const filterCategory = ref('')
const filterBrand = ref('')
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)

const productForm = ref({
  name: '',
  name_ar: '',
  barcode: '',
  sku: '',
  category_id: '',
  brand_id: '',
  cost_price: 0,
  selling_price: 0,
  stock_quantity: 0,
  min_stock_level: 10,
  size: '',
  is_active: true
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchSearch = !searchQuery.value || 
      p.name_ar.includes(searchQuery.value) ||
      p.name.includes(searchQuery.value) ||
      p.barcode.includes(searchQuery.value)
    const matchCategory = !filterCategory.value || p.category_id === filterCategory.value
    const matchBrand = !filterBrand.value || p.brand_id === filterBrand.value
    return matchSearch && matchCategory && matchBrand
  })
})


const fetchProducts = async () => {
  try {
    const response = await api.getProducts()
    products.value = response.data
  } catch (error) {
    toast.error('فشل تحميل المنتجات')
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.getCategories()
    categories.value = response.data
  } catch (error) {
    toast.error('فشل تحميل التصنيفات')
  }
}

const fetchBrands = async () => {
  try {
    const response = await api.getBrands()
    brands.value = response.data
  } catch (error) {
    toast.error('فشل تحميل العلامات التجارية')
  }
}

const submitProduct = async () => {
  try {
    if (showEditModal.value) {
      await api.updateProduct(editingId.value, productForm.value)
      toast.success('تم تحديث المنتج بنجاح')
    } else {
      await api.createProduct(productForm.value)
      toast.success('تم إضافة المنتج بنجاح')
    }
    closeModal()
    fetchProducts()
  } catch (error) {
    toast.error(error.response?.data?.message || 'حدث خطأ')
  }
}

const editProduct = (product) => {
  productForm.value = { ...product }
  editingId.value = product.id
  showEditModal.value = true
}

const deleteProduct = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return
  
  try {
    await api.deleteProduct(id)
    toast.success('تم حذف المنتج بنجاح')
    fetchProducts()
  } catch (error) {
    toast.error('فشل حذف المنتج')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingId.value = null
  productForm.value = {
    name: '',
    name_ar: '',
    barcode: '',
    sku: '',
    category_id: '',
    brand_id: '',
    cost_price: 0,
    selling_price: 0,
    stock_quantity: 0,
    min_stock_level: 10,
    size: '',
    is_active: true
  }
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
  fetchBrands()
})
</script>
