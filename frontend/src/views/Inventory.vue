<template>
  <div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="card bg-gradient-to-br from-red-500 to-red-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-red-100 text-sm">إجمالي المنتجات التالفة</p>
            <h3 class="text-3xl font-bold mt-2">{{ toLatinNumbers(damagedStats.total_damaged || 0) }}</h3>
          </div>
          <AlertTriangle :size="48" class="text-red-200" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm">الكمية التالفة</p>
            <h3 class="text-3xl font-bold mt-2">{{ toLatinNumbers(damagedStats.total_quantity || 0) }}</h3>
          </div>
          <Package :size="48" class="text-orange-200" />
        </div>
      </div>

      <div class="card bg-gradient-to-br from-gray-600 to-gray-700 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-100 text-sm">قيمة الخسائر</p>
            <h3 class="text-2xl font-bold mt-2">{{ formatCurrencyLatin(damagedStats.total_loss || 0) }}</h3>
          </div>
          <DollarSign :size="48" class="text-gray-200" />
        </div>
      </div>
    </div>

    <!-- Header and Add Button -->
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">إدارة المنتجات التالفة</h2>
        <button @click="openAddDamagedModal" class="btn btn-danger flex items-center gap-2 whitespace-nowrap">
          <Plus :size="20" />
          تسجيل منتج تالف
        </button>
      </div>


      <!-- Damaged Products Table -->
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>SKU</th>
              <th>المنتج</th>
              <th>الفئة</th>
              <th>الكمية المتضررة</th>
              <th>حد إعادة الطلب</th>
              <th>المتاح للبيع</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in damagedProducts" :key="item.id">
              <td class="font-mono text-sm">{{ item.sku }}</td>
              <td>
                <div>
                  <p class="font-medium">{{ item.product_name }}</p>
                  <p class="text-xs text-gray-500">{{ item.category_name }}</p>
                </div>
              </td>
              <td>{{ item.category_name }}</td>
              <td>
                <span class="badge badge-danger">
                  {{ toLatinNumbers(item.damaged_quantity) }} قطعة
                </span>
              </td>
              <td>{{ toLatinNumbers(item.reorder_level) }}</td>
              <td>
                <span class="badge badge-success">
                  {{ toLatinNumbers(item.available_stock) }} قطعة
                </span>
              </td>
              <td>
                <span :class="[
                  'badge',
                  item.status === 'متوفر' ? 'badge-success' : 'badge-warning'
                ]">
                  {{ item.status }}
                </span>
              </td>
              <td>
                <div class="flex items-center gap-2">
                  <button @click="viewDamagedDetails(item)" class="text-blue-600 hover:text-blue-800" title="عرض التفاصيل">
                    <Eye :size="18" />
                  </button>
                  <button @click="deleteDamagedRecord(item)" class="text-red-600 hover:text-red-800" title="حذف">
                    <Trash2 :size="18" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="damagedProducts.length === 0">
              <td colspan="8" class="text-center py-8 text-gray-500">
                لا توجد منتجات تالفة مسجلة
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- View Details Modal -->
    <div v-if="detailsItem" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="detailsItem = null">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-2xl font-bold text-blue-600">تفاصيل المنتج التالف</h3>
          <button @click="detailsItem = null" class="text-gray-400 hover:text-gray-600">
            <X :size="24" />
          </button>
        </div>
        
        <div class="space-y-4">
          <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
            <Info :size="20" class="text-blue-600 mt-1" />
            <div class="flex-1">
              <div class="font-bold text-gray-800">{{ detailsItem.product_name }}</div>
              <div class="text-sm text-gray-600">SKU: {{ detailsItem.sku }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-3 bg-gray-50 rounded-lg">
              <div class="text-xs text-gray-500 mb-1">الفئة</div>
              <div class="font-semibold">{{ detailsItem.category_name }}</div>
            </div>
            <div class="p-3 bg-red-50 rounded-lg">
              <div class="text-xs text-gray-500 mb-1">الكمية التالفة</div>
              <div class="font-semibold text-red-600">{{ toLatinNumbers(detailsItem.damaged_quantity) }} قطعة</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-3 bg-gray-50 rounded-lg">
              <div class="text-xs text-gray-500 mb-1">المتاح للبيع</div>
              <div class="font-semibold text-green-600">{{ toLatinNumbers(detailsItem.available_stock) }} قطعة</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
              <div class="text-xs text-gray-500 mb-1">حد إعادة الطلب</div>
              <div class="font-semibold">{{ toLatinNumbers(detailsItem.reorder_level) }}</div>
            </div>
          </div>

          <div class="p-3 bg-yellow-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">نوع التلف</div>
            <div class="font-semibold text-yellow-700">{{ getDamageTypeLabel(detailsItem.damage_type) }}</div>
          </div>

          <div class="p-3 bg-gray-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">قيمة الخسارة</div>
            <div class="font-semibold text-red-600">{{ formatCurrencyLatin(detailsItem.total_loss || (detailsItem.cost_price * detailsItem.damaged_quantity)) }}</div>
          </div>

          <div v-if="detailsItem.notes" class="p-3 bg-gray-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">ملاحظات</div>
            <div class="text-sm">{{ detailsItem.notes }}</div>
          </div>

          <div class="p-3 bg-gray-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">تاريخ التسجيل</div>
            <div class="text-sm">{{ formatDateLatin(detailsItem.created_at) }}</div>
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <button @click="detailsItem = null" class="btn btn-secondary flex-1">إغلاق</button>
          <button @click="deleteDamagedRecord(detailsItem); detailsItem = null" class="btn btn-danger flex-1">
            <Trash2 :size="18" class="inline" />
            حذف
          </button>
        </div>
      </div>
    </div>

    <!-- Add Damaged Product Modal -->
    <div v-if="showDamagedModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeDamagedModal">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-6 text-red-600">تسجيل منتج تالف</h3>
        
        <form @submit.prevent="submitDamagedProduct" class="space-y-4">
          <!-- Product Search Dropdown -->
          <div>
            <label class="block text-sm font-medium mb-2">المنتج *</label>
            <div class="relative">
              <input
                v-model="productSearchQuery"
                @input="handleProductSearch"
                @focus="showProductDropdown = true"
                @blur="hideProductDropdown"
                type="text"
                placeholder="ابحث عن منتج بالاسم أو SKU..."
                class="input"
                required
              />
              <div 
                v-if="showProductDropdown && filteredProducts.length > 0"
                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <div
                  v-for="product in filteredProducts"
                  :key="product.id"
                  @mousedown="selectProduct(product)"
                  class="px-4 py-3 hover:bg-red-50 cursor-pointer transition-colors border-b last:border-b-0"
                  :class="{ 'bg-red-100': damagedForm.product_id === product.id }"
                >
                  <div class="font-medium">{{ product.name_ar }}</div>
                  <div class="text-sm text-gray-600 flex justify-between mt-1">
                    <span>SKU: {{ product.sku }}</span>
                    <span class="font-semibold text-green-600">المخزون: {{ toLatinNumbers(product.stock_quantity) }}</span>
                  </div>
                </div>
              </div>
            </div>
            <p v-if="selectedProduct" class="text-sm text-gray-600 mt-1">
              المخزون الحالي: <span class="font-bold text-green-600">{{ toLatinNumbers(selectedProduct.stock_quantity) }}</span> قطعة
            </p>
          </div>

          <!-- Damaged Quantity -->
          <div>
            <label class="block text-sm font-medium mb-2">الكمية التالفة *</label>
            <input 
              v-model.number="damagedForm.quantity" 
              type="number" 
              min="1"
              :max="selectedProduct?.stock_quantity || 999"
              required 
              class="input" 
              placeholder="أدخل الكمية التالفة"
            />
            <p class="text-xs text-red-600 mt-1">
              ⚠️ سيتم خصم هذه الكمية من المخزون تلقائياً
            </p>
          </div>

          <!-- Damage Type -->
          <div>
            <label class="block text-sm font-medium mb-2">نوع التلف *</label>
            <select v-model="damagedForm.damage_type" required class="input">
              <option value="">اختر نوع التلف</option>
              <option value="expired">منتهي الصلاحية</option>
              <option value="broken">مكسور/تالف</option>
              <option value="defective">معيب من المصنع</option>
              <option value="water_damage">تلف بسبب الماء</option>
              <option value="other">أخرى</option>
            </select>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium mb-2">ملاحظات</label>
            <textarea 
              v-model="damagedForm.notes" 
              rows="3" 
              class="input"
              placeholder="أضف أي ملاحظات إضافية..."
            ></textarea>
          </div>
          
          <div class="flex gap-3 justify-end mt-6">
            <button type="button" @click="closeDamagedModal" class="btn btn-secondary">إلغاء</button>
            <button type="submit" class="btn btn-danger">تسجيل التلف</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Package, AlertTriangle, DollarSign, Plus, Eye, Trash2, Info, X } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'

const toast = useToast()
const allProducts = ref([])
const damagedProducts = ref([])
const detailsItem = ref(null)
const damagedStats = ref({
  total_damaged: 0,
  total_quantity: 0,
  total_loss: 0
})
const showDamagedModal = ref(false)
const showProductModal = ref(false)
const productSearchQuery = ref('')
const showProductDropdown = ref(false)
const selectedProduct = ref(null)

const damagedForm = ref({
  product_id: null,
  quantity: 1,
  damage_type: '',
  notes: ''
})

const filteredProducts = computed(() => {
  if (!productSearchQuery.value) return allProducts.value.slice(0, 10)
  
  const query = productSearchQuery.value.toLowerCase().trim()
  
  return allProducts.value.filter(product => {
    const name = product.name_ar.toLowerCase()
    const sku = product.sku?.toLowerCase() || ''
    const barcode = product.barcode?.toLowerCase() || ''
    
    // Exact match
    if (name.includes(query) || sku.includes(query) || barcode.includes(query)) {
      return true
    }
    
    // Fuzzy match
    let queryIndex = 0
    for (let i = 0; i < name.length && queryIndex < query.length; i++) {
      if (name[i] === query[queryIndex]) {
        queryIndex++
      }
    }
    return queryIndex === query.length
  }).slice(0, 10)
})


const fetchAllProducts = async () => {
  try {
    const response = await api.getProducts({ per_page: 1000 })
    allProducts.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Failed to load products:', error)
    if (error.response?.status === 401) {
      // Authentication error - will be handled by interceptor
      return
    }
    toast.error('فشل تحميل المنتجات')
    allProducts.value = []
  }
}

const fetchDamagedProducts = async () => {
  try {
    const response = await api.getDamagedProducts()
    damagedProducts.value = response.data || []
    await fetchDamagedStats()
  } catch (error) {
    console.error('Failed to load damaged products:', error)
    damagedProducts.value = []
  }
}

const fetchDamagedStats = async () => {
  try {
    const response = await api.getDamagedStats()
    damagedStats.value = response.data
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
}

const calculateDamagedStats = () => {
  damagedStats.value = {
    total_damaged: damagedProducts.value.length,
    total_quantity: damagedProducts.value.reduce((sum, item) => sum + item.damaged_quantity, 0),
    total_loss: damagedProducts.value.reduce((sum, item) => sum + (item.damaged_quantity * item.cost_price), 0)
  }
}

const handleProductSearch = () => {
  showProductDropdown.value = true
}

const selectProduct = (product) => {
  selectedProduct.value = product
  damagedForm.value.product_id = product.id
  productSearchQuery.value = `${product.name_ar} (${product.sku})`
  showProductDropdown.value = false
}

const hideProductDropdown = () => {
  setTimeout(() => {
    showProductDropdown.value = false
  }, 200)
}

const openAddDamagedModal = () => {
  damagedForm.value = {
    product_id: null,
    quantity: 1,
    damage_type: '',
    notes: ''
  }
  selectedProduct.value = null
  productSearchQuery.value = ''
  showDamagedModal.value = true
}

const closeDamagedModal = () => {
  showDamagedModal.value = false
  selectedProduct.value = null
  productSearchQuery.value = ''
}

const submitDamagedProduct = async () => {
  try {
    // Validation
    if (!selectedProduct.value) {
      toast.error('الرجاء اختيار منتج')
      return
    }

    if (!damagedForm.value.quantity || damagedForm.value.quantity <= 0) {
      toast.error('الرجاء إدخال كمية صحيحة')
      return
    }

    if (damagedForm.value.quantity > selectedProduct.value.stock_quantity) {
      toast.error('الكمية المطلوبة أكبر من المخزون المتاح')
      return
    }

    if (!damagedForm.value.damage_type) {
      toast.error('الرجاء اختيار نوع التلف')
      return
    }

    // Create damaged product record (this will also deduct from inventory)
    try {
      console.log('Creating damaged product:', {
        product_id: selectedProduct.value.id,
        quantity: damagedForm.value.quantity,
        damage_type: damagedForm.value.damage_type,
        notes: damagedForm.value.notes || ''
      })
      
      const response = await api.createDamagedProduct({
        product_id: selectedProduct.value.id,
        quantity: damagedForm.value.quantity,
        damage_type: damagedForm.value.damage_type,
        notes: damagedForm.value.notes || ''
      })
      
      console.log('✅ Damaged product created:', response.data)
      
      toast.success('✅ تم تسجيل المنتج التالف وخصمه من المخزون')
      closeDamagedModal()
      
      // Refresh data
      await fetchAllProducts()
      await fetchDamagedProducts()
      
    } catch (apiError) {
      console.error('API Error Full:', apiError)
      console.error('API Error Response:', apiError.response)
      console.error('API Error Data:', apiError.response?.data)
      
      // Extract detailed error message
      let errorMessage = 'فشل في تسجيل المنتج التالف'
      if (apiError.response?.data) {
        if (apiError.response.data.errors) {
          // Laravel validation errors
          const errors = Object.values(apiError.response.data.errors).flat()
          errorMessage = errors.join(', ')
        } else if (apiError.response.data.error) {
          errorMessage = apiError.response.data.error
        } else if (apiError.response.data.message) {
          errorMessage = apiError.response.data.message
        }
      }
      
      throw new Error(errorMessage)
    }
  } catch (error) {
    console.error('Submit Error:', error)
    toast.error(error.message || error.response?.data?.error || 'فشل تسجيل المنتج التالف')
  }
}

const getDamageTypeLabel = (type) => {
  const labels = {
    'expired': 'منتهي الصلاحية',
    'broken': 'مكسور/تالف',
    'defective': 'معيب من المصنع',
    'water_damage': 'تلف بسبب الماء',
    'other': 'أخرى'
  }
  return labels[type] || type
}

const viewDamagedDetails = (item) => {
  detailsItem.value = item
}

const deleteDamagedRecord = async (item) => {
  const confirmMsg = `هل أنت متأكد من حذف هذا السجل؟\n\nسيتم استعادة ${item.damaged_quantity} قطعة إلى المخزون`
  if (!confirm(confirmMsg)) return
  
  try {
    const response = await api.deleteDamagedProduct(item.id)
    console.log('Delete response:', response.data)
    
    const message = response.data.message || 'تم حذف السجل واستعادة الكمية للمخزون'
    toast.success(`✅ ${message}`)
    
    // Refresh both damaged products and inventory
    await fetchDamagedProducts()
    await fetchAllProducts()
  } catch (error) {
    console.error('Delete error:', error)
    const errorMsg = error.response?.data?.message || error.response?.data?.error || 'فشل حذف السجل'
    toast.error(errorMsg)
  }
}

// Format date with Latin digits (yyyy-mm-dd HH:MM)
const formatDateLatin = (value) => {
  try {
    const d = new Date(value)
    const pad = (n) => String(n).padStart(2, '0')
    const yyyy = d.getFullYear()
    const mm = pad(d.getMonth() + 1)
    const dd = pad(d.getDate())
    const HH = pad(d.getHours())
    const MM = pad(d.getMinutes())
    return `${yyyy}-${mm}-${dd} ${HH}:${MM}`
  } catch (e) {
    return ''
  }
}

onMounted(() => {
  fetchAllProducts()
  fetchDamagedProducts()
})
</script>
