# ✅ Products Module - Complete Restructure Summary

## Status: Backend Complete ✅ | Frontend Pending ⏳

---

## Database ✅ DONE

### Final Structure:
```
products table:
- id
- name (English)
- name_ar (Arabic)
- sku
- selling_price (base price)
- price_جملة (wholesale - 85%)
- price_قطاعي (retail - 100%)
- price_صفحة (online - 110%)
- volume_ml (50/100/150/200)
- quantity
- alert_quantity
- photos (JSON)
- is_active
- created_at, updated_at
```

### Removed:
- description, category_id, brand_id, cost_price, barcode, reserved_qty, size, image

---

## Backend ✅ DONE

### Product Model Updated:
- ✅ Fillable fields match new structure
- ✅ Casts for all fields
- ✅ `is_low_stock` computed attribute
- ✅ `getPriceForSegment($segment)` method
- ✅ Removed category/brand relationships

### ProductController Updated:
- ✅ `index()` - Returns new fields, supports low_stock filter
- ✅ `store()` - Auto-calculates segment prices
- ✅ `update()` - Recalculates prices when base price changes
- ✅ `show()` - No category/brand loading
- ✅ `searchBySku()` - Renamed from searchByBarcode

---

## Frontend ⏳ PENDING

### File to Update: `frontend/src/views/StockList.vue`

Replace entire content with:

```vue
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          @input="fetchProducts"
          type="text"
          placeholder="بحث بالاسم أو SKU..."
          class="input w-80"
        />
        <label class="flex items-center gap-2 cursor-pointer">
          <input 
            type="checkbox" 
            v-model="showLowStock" 
            @change="fetchProducts"
            class="w-4 h-4"
          />
          <span>عرض المنتجات المنخفضة فقط</span>
        </label>
      </div>
      <button @click="openAddModal" class="btn btn-primary flex items-center gap-2">
        <Plus :size="20" />
        إضافة منتج
      </button>
    </div>

    <!-- Products Table -->
    <div class="card overflow-hidden">
      <table class="table">
        <thead>
          <tr>
            <th>SKU</th>
            <th>المنتج</th>
            <th>الحجم</th>
            <th>سعر جملة</th>
            <th>سعر قطاعي</th>
            <th>سعر صفحة</th>
            <th>الكمية</th>
            <th>حد التنبيه</th>
            <th>الحالة</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="product in products" 
            :key="product.id"
            :class="{'bg-red-50': product.is_low_stock}"
          >
            <td class="font-mono text-sm">{{ product.sku }}</td>
            <td class="font-medium">{{ product.name_ar }}</td>
            <td class="text-center">{{ product.volume_ml }} مل</td>
            <td class="text-green-600 font-semibold">
              {{ formatPrice(product.price_جملة) }}
            </td>
            <td class="text-blue-600 font-semibold">
              {{ formatPrice(product.price_قطاعي) }}
            </td>
            <td class="text-yellow-600 font-semibold">
              {{ formatPrice(product.price_صفحة) }}
            </td>
            <td>
              <span :class="product.is_low_stock ? 'text-red-600 font-bold' : ''">
                {{ product.quantity }}
              </span>
            </td>
            <td class="text-gray-500">{{ product.alert_quantity }}</td>
            <td>
              <span 
                v-if="product.is_low_stock" 
                class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium"
              >
                منخفض
              </span>
              <span 
                v-else 
                class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium"
              >
                متوفر
              </span>
            </td>
            <td>
              <div class="flex items-center gap-2">
                <button 
                  @click="editProduct(product)" 
                  class="text-blue-600 hover:text-blue-800"
                  title="تعديل"
                >
                  <Edit :size="18" />
                </button>
                <button 
                  @click="deleteProduct(product.id)" 
                  class="text-red-600 hover:text-red-800"
                  title="حذف"
                >
                  <Trash2 :size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="products.length === 0" class="text-center py-8 text-gray-500">
        لا توجد منتجات
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-bold mb-6">
          {{ isEditing ? 'تعديل منتج' : 'إضافة منتج جديد' }}
        </h3>
        
        <form @submit.prevent="submitProduct" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <!-- Arabic Name -->
            <div>
              <label class="block text-sm font-medium mb-2">
                الاسم بالعربي <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.name_ar" 
                required 
                class="input w-full"
                placeholder="عطر الورد"
              />
            </div>

            <!-- English Name -->
            <div>
              <label class="block text-sm font-medium mb-2">
                الاسم بالإنجليزي <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.name" 
                required 
                class="input w-full"
                placeholder="Rose Perfume"
              />
            </div>

            <!-- SKU -->
            <div>
              <label class="block text-sm font-medium mb-2">
                SKU <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.sku" 
                required 
                class="input w-full"
                placeholder="PRF-001"
              />
            </div>

            <!-- Volume -->
            <div>
              <label class="block text-sm font-medium mb-2">
                الحجم (مل) <span class="text-red-500">*</span>
              </label>
              <select v-model.number="form.volume_ml" required class="input w-full">
                <option :value="50">50 مل</option>
                <option :value="100">100 مل</option>
                <option :value="150">150 مل</option>
                <option :value="200">200 مل</option>
              </select>
            </div>

            <!-- Base Price -->
            <div>
              <label class="block text-sm font-medium mb-2">
                السعر الأساسي <span class="text-red-500">*</span>
              </label>
              <input 
                v-model.number="form.selling_price" 
                type="number" 
                step="0.01" 
                required 
                class="input w-full"
                placeholder="100.00"
              />
              <div class="text-xs text-gray-500 mt-1">
                <div class="flex justify-between">
                  <span>جملة:</span>
                  <span class="font-medium text-green-600">
                    {{ (form.selling_price * 0.85).toFixed(2) }} جنيه
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>قطاعي:</span>
                  <span class="font-medium text-blue-600">
                    {{ form.selling_price.toFixed(2) }} جنيه
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>صفحة:</span>
                  <span class="font-medium text-yellow-600">
                    {{ (form.selling_price * 1.1).toFixed(2) }} جنيه
                  </span>
                </div>
              </div>
            </div>

            <!-- Quantity -->
            <div>
              <label class="block text-sm font-medium mb-2">
                الكمية <span class="text-red-500">*</span>
              </label>
              <input 
                v-model.number="form.quantity" 
                type="number" 
                required 
                min="0"
                class="input w-full"
                placeholder="100"
              />
            </div>

            <!-- Alert Quantity -->
            <div>
              <label class="block text-sm font-medium mb-2">
                حد التنبيه <span class="text-red-500">*</span>
              </label>
              <input 
                v-model.number="form.alert_quantity" 
                type="number" 
                required 
                min="1"
                class="input w-full"
                placeholder="10"
              />
              <p class="text-xs text-gray-500 mt-1">
                سيتم التنبيه عندما تصل الكمية لهذا الحد
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
            <button 
              type="button" 
              @click="closeModal" 
              class="btn btn-secondary"
            >
              إلغاء
            </button>
            <button 
              type="submit" 
              class="btn btn-primary"
            >
              {{ isEditing ? 'تحديث' : 'إضافة' }}
            </button>
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
const products = ref([])
const searchQuery = ref('')
const showLowStock = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = ref({
  name: '',
  name_ar: '',
  sku: '',
  selling_price: 0,
  volume_ml: 100,
  quantity: 0,
  alert_quantity: 10
})

const fetchProducts = async () => {
  try {
    const params = {
      search: searchQuery.value,
      low_stock: showLowStock.value ? 1 : 0
    }
    const response = await api.getProducts(params)
    products.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching products:', error)
    toast.error('فشل تحميل المنتجات')
  }
}

const openAddModal = () => {
  isEditing.value = false
  form.value = {
    name: '',
    name_ar: '',
    sku: '',
    selling_price: 0,
    volume_ml: 100,
    quantity: 0,
    alert_quantity: 10
  }
  showModal.value = true
}

const editProduct = (product) => {
  isEditing.value = true
  editingId.value = product.id
  form.value = {
    name: product.name,
    name_ar: product.name_ar,
    sku: product.sku,
    selling_price: parseFloat(product.selling_price),
    volume_ml: product.volume_ml,
    quantity: product.quantity,
    alert_quantity: product.alert_quantity
  }
  showModal.value = true
}

const submitProduct = async () => {
  try {
    if (isEditing.value) {
      await api.updateProduct(editingId.value, form.value)
      toast.success('تم تحديث المنتج بنجاح')
    } else {
      await api.createProduct(form.value)
      toast.success('تم إضافة المنتج بنجاح')
    }
    closeModal()
    fetchProducts()
  } catch (error) {
    console.error('Error saving product:', error)
    toast.error('حدث خطأ أثناء حفظ المنتج')
  }
}

const deleteProduct = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) return
  
  try {
    await api.deleteProduct(id)
    toast.success('تم حذف المنتج بنجاح')
    fetchProducts()
  } catch (error) {
    console.error('Error deleting product:', error)
    toast.error('فشل حذف المنتج')
  }
}

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  editingId.value = null
}

const formatPrice = (price) => {
  return `${parseFloat(price).toFixed(2)} جنيه`
}

onMounted(() => {
  fetchProducts()
})
</script>
```

---

## Next Steps:

1. ✅ Database restructured
2. ✅ Backend updated
3. ⏳ **Replace `frontend/src/views/StockList.vue` with code above**
4. ⏳ **Run:** `npm run build`
5. ⏳ **Test:** Add/Edit/Delete products

---

## Testing Checklist:

- [ ] View products list
- [ ] Search by name/SKU
- [ ] Filter low stock
- [ ] Add new product
- [ ] Edit product
- [ ] Delete product
- [ ] Verify segment prices auto-calculate
- [ ] Check low stock badge appears

**All code is ready! Just update StockList.vue and rebuild!** 🎉
