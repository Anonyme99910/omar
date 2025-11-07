<template>
  <div class="stock-container">
    <div class="header">
      <h1>إدارة المخزون</h1>
      <div class="header-actions">
        <router-link to="/import-products" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          📊 استيراد منتجات
        </router-link>
        <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          <Plus :size="20" />
          {{ viewMode === 'products' ? 'إضافة منتج' : 'إضافة باقة' }}
        </button>
      </div>
    </div>

    <!-- View Mode Toggle -->
    <div class="flex justify-center mb-6">
      <div class="inline-flex rounded-lg border-2 border-primary-600 p-1 bg-gray-100">
        <button
          @click="viewMode = 'products'"
          :class="[
            'px-6 py-2 rounded-md font-medium transition-all',
            viewMode === 'products' 
              ? 'bg-primary-600 text-white shadow-md' 
              : 'text-gray-700 hover:bg-gray-200'
          ]"
        >
          المنتجات
        </button>
        <button
          @click="viewMode = 'packages'"
          :class="[
            'px-6 py-2 rounded-md font-medium transition-all',
            viewMode === 'packages' 
              ? 'bg-primary-600 text-white shadow-md' 
              : 'text-gray-700 hover:bg-gray-200'
          ]"
        >
          الباقات
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input 
        v-model="searchQuery" 
        type="text" 
        :placeholder="viewMode === 'products' ? 'بحث بالاسم أو SKU...' : 'بحث عن باقة...'"
        class="search-input"
        @input="viewMode === 'products' ? fetchProducts() : fetchPackages()"
      >
      <label class="checkbox-label">
        <input type="checkbox" v-model="showLowStock" @change="viewMode === 'products' ? fetchProducts() : fetchPackages()">
        <span>عرض {{ viewMode === 'products' ? 'المنتجات' : 'الباقات' }} منخفضة المخزون فقط</span>
      </label>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table v-if="viewMode === 'products'">
        <thead>
          <tr>
            <th>المنتج</th>
            <th>الحجم</th>
            <th>سعر جملة</th>
            <th>سعر قطاعي</th>
            <th>سعر صفحة</th>
            <th>الكمية</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td class="font-medium">{{ product.name_ar }}</td>
            <td class="text-center">{{ product.volume_ml }}</td>
            <td class="text-green-600 font-semibold">{{ formatPrice(product.price_جملة) }}</td>
            <td class="text-blue-600 font-semibold">{{ formatPrice(product.price_قطاعي) }}</td>
            <td class="text-yellow-600 font-semibold">{{ formatPrice(product.price_صفحة) }}</td>
            <td class="text-center">{{ product.quantity }}</td>
            <td class="actions">
              <button @click="editProduct(product)" class="btn-icon" title="تعديل">
                <Edit :size="18" />
              </button>
              <button @click="deleteProduct(product.id)" class="btn-icon text-red-600" title="حذف">
                <Trash2 :size="18" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <table v-else>
        <thead>
          <tr>
            <th>الباقة</th>
            <th>سعر جملة</th>
            <th>سعر قطاعي</th>
            <th>سعر صفحة</th>
            <th>الكمية</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pkg in packages" :key="pkg.id">
            <td class="font-medium">{{ pkg.name_ar }}</td>
            <td class="text-green-600 font-semibold">{{ formatPrice(pkg.price_جملة) }}</td>
            <td class="text-blue-600 font-semibold">{{ formatPrice(pkg.price_قطاعي) }}</td>
            <td class="text-yellow-600 font-semibold">{{ formatPrice(pkg.price_صفحة) }}</td>
            <td class="text-center">{{ pkg.quantity }}</td>
            <td class="actions">
              <button @click="editPackage(pkg)" class="btn-icon" title="تعديل">
                <Edit :size="18" />
              </button>
              <button @click="deletePackage(pkg.id)" class="btn-icon text-red-600" title="حذف">
                <Trash2 :size="18" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="(viewMode === 'products' && products.length === 0) || (viewMode === 'packages' && packages.length === 0)" class="empty-state">
        {{ viewMode === 'products' ? 'لا توجد منتجات' : 'لا توجد باقات' }}
      </div>
    </div>

    <!-- Add/Edit Product/Package Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal modal-large" @click.stop>
        <div class="modal-header">
          <h3>
            {{ viewMode === 'products' 
              ? (isEditing ? 'تعديل منتج' : 'إضافة منتج جديد')
              : (isEditing ? 'تعديل باقة' : 'إضافة باقة جديدة')
            }}
          </h3>
          <button @click="closeModal" class="btn-close">×</button>
        </div>
        <div class="modal-body">
          <form v-if="viewMode === 'products'" @submit.prevent="submitProduct">
            <div class="form-row">
              <div class="form-group">
                <label>اسم المنتج (عربي) *</label>
                <input v-model="form.name_ar" type="text" required placeholder="عطر الورد">
              </div>
              <div class="form-group">
                <label>الحجم (مل)</label>
                <input v-model="form.volume_ml" type="text" placeholder="100 مل">
                <small class="text-gray-500">مثال: 50 مل، 100 مل، إلخ</small>
              </div>
            </div>

            <div class="form-group">
              <label>صورة المنتج</label>
              <input type="file" @change="handlePhotoUpload" accept="image/*" class="file-input">
              <small class="text-gray-500">اختياري - الحد الأقصى: 5MB - صيغ مدعومة: JPG, PNG, WEBP</small>
              <div v-if="photoPreview" class="photo-preview">
                <img :src="photoPreview" alt="Preview" />
              </div>
            </div>

            <div class="form-group">
              <label>تكلفة الإنتاج *</label>
              <input v-model.number="form.production_cost" type="number" step="0.01" required placeholder="50.00" class="price-input-cost">
              <small class="text-gray-500">تكلفة تصنيع/شراء المنتج</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>سعر الجملة *</label>
                <input 
                  v-model.number="form.price_جملة" 
                  @input="syncPrices"
                  type="number" 
                  step="0.01" 
                  required 
                  placeholder="85.00" 
                  class="price-input-wholesale"
                >
                <small class="text-gray-500">سعر البيع للعملاء الجملة (سيتم نسخه للأسعار الأخرى)</small>
              </div>
              <div class="form-group">
                <label>سعر القطاعي *</label>
                <input v-model.number="form.price_قطاعي" type="number" step="0.01" required placeholder="100.00" class="price-input-retail">
                <small class="text-gray-500">سعر البيع للعملاء القطاعي</small>
              </div>
            </div>

            <div class="form-group">
              <label>سعر صفحة *</label>
              <input v-model.number="form.price_صفحة" type="number" step="0.01" required placeholder="110.00" class="price-input-online">
              <small class="text-gray-500">سعر البيع للعملاء صفحة</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>الكمية *</label>
                <input v-model.number="form.quantity" type="number" required min="0" placeholder="100">
              </div>
              <div class="form-group">
                <label>حد التنبيه *</label>
                <input v-model.number="form.alert_quantity" type="number" required min="1" placeholder="10">
                <small class="text-gray-500">سيتم التنبيه عندما تصل الكمية لهذا الحد</small>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-secondary">إلغاء</button>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                {{ isEditing ? 'تحديث' : 'إضافة' }}
              </button>
            </div>
          </form>

          <!-- Package Form -->
          <form v-else @submit.prevent="submitPackage">
            <div class="form-row">
              <div class="form-group">
                <label>اسم الباقة (عربي) *</label>
                <input v-model="packageForm.name_ar" type="text" required placeholder="باقة العطور المميزة">
              </div>
            </div>

            <div class="form-group">
              <label>الوصف</label>
              <textarea v-model="packageForm.description" rows="3" placeholder="وصف الباقة..."></textarea>
            </div>

            <!-- Photo Upload -->
            <div class="form-group">
              <label>صورة الباقة</label>
              <input type="file" @change="handlePhotoUpload" accept="image/*" class="file-input">
              <div v-if="photoPreview" class="photo-preview">
                <img :src="photoPreview" alt="Preview">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>سعر الجملة *</label>
                <input v-model.number="packageForm.price_جملة" type="number" step="0.01" required placeholder="75.00" class="price-input-wholesale">
                <small class="text-gray-500">سعر البيع للعملاء جملة</small>
              </div>
              <div class="form-group">
                <label>سعر القطاعي *</label>
                <input v-model.number="packageForm.price_قطاعي" type="number" step="0.01" required placeholder="93.50" class="price-input-retail">
                <small class="text-gray-500">سعر البيع للعملاء قطاعي</small>
              </div>
            </div>

            <div class="form-group">
              <label>سعر الصفحة *</label>
              <input v-model.number="packageForm.price_صفحة" type="number" step="0.01" required placeholder="110.00" class="price-input-online">
              <small class="text-gray-500">سعر البيع للعملاء صفحة</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>الكمية *</label>
                <input v-model.number="packageForm.quantity" type="number" required min="0" placeholder="50">
              </div>
              <div class="form-group">
                <label>حد التنبيه *</label>
                <input v-model.number="packageForm.alert_quantity" type="number" required min="1" placeholder="10">
                <small class="text-gray-500">سيتم التنبيه عندما تصل الكمية لهذا الحد</small>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-secondary">إلغاء</button>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                {{ isEditing ? 'تحديث' : 'إضافة' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Edit, Trash2 } from 'lucide-vue-next'
import api from '../../services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()

const viewMode = ref('products') // 'products' or 'packages'
const products = ref([])
const packages = ref([])
const searchQuery = ref('')
const showLowStock = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const photoFile = ref(null)
const photoPreview = ref(null)

const form = ref({
  name_ar: '',
  production_cost: 0,
  price_جملة: 0,
  price_قطاعي: 0,
  price_صفحة: 0,
  volume_ml: '',
  quantity: 0,
  alert_quantity: 10
})

// Sync all prices to match جملة price
const syncPrices = () => {
  if (form.value.price_جملة > 0) {
    form.value.price_قطاعي = form.value.price_جملة
    form.value.price_صفحة = form.value.price_جملة
  }
}

const packageForm = ref({
  name_ar: '',
  description: '',
  price_جملة: 0,
  price_قطاعي: 0,
  price_صفحة: 0,
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

const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    photoFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const openAddModal = () => {
  isEditing.value = false
  photoFile.value = null
  photoPreview.value = null
  
  if (viewMode.value === 'products') {
    form.value = {
      name_ar: '',
      price_جملة: 0,
      price_قطاعي: 0,
      price_صفحة: 0,
      volume_ml: '',
      quantity: 0,
      alert_quantity: 10
    }
  } else {
    packageForm.value = {
      name_ar: '',
      description: '',
      price_جملة: 0,
      price_قطاعي: 0,
      price_صفحة: 0,
      quantity: 0,
      alert_quantity: 10
    }
  }
  
  showModal.value = true
}

const editProduct = (product) => {
  isEditing.value = true
  editingId.value = product.id
  form.value = {
    name_ar: product.name_ar,
    price_جملة: parseFloat(product.price_جملة),
    price_قطاعي: parseFloat(product.price_قطاعي),
    price_صفحة: parseFloat(product.price_صفحة),
    volume_ml: product.volume_ml || '',
    quantity: product.quantity,
    alert_quantity: product.alert_quantity
  }
  showModal.value = true
}

const submitProduct = async () => {
  try {
    const formData = new FormData()
    
    // Add all form fields - ensure proper values
    if (form.value.name_ar) formData.append('name_ar', form.value.name_ar)
    if (form.value.volume_ml) formData.append('volume_ml', form.value.volume_ml)
    
    // Ensure prices are numbers
    formData.append('price_جملة', parseFloat(form.value.price_جملة) || 0)
    formData.append('price_قطاعي', parseFloat(form.value.price_قطاعي) || 0)
    formData.append('price_صفحة', parseFloat(form.value.price_صفحة) || 0)
    
    formData.append('quantity', parseInt(form.value.quantity) || 0)
    formData.append('alert_quantity', parseInt(form.value.alert_quantity) || 10)
    
    // Add photo if selected
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }
    
    if (isEditing.value) {
      formData.append('_method', 'PUT')
      await api.updateProduct(editingId.value, formData)
      toast.success('تم تحديث المنتج بنجاح')
    } else {
      await api.createProduct(formData)
      toast.success('تم إضافة المنتج بنجاح')
    }
    closeModal()
    fetchProducts()
  } catch (error) {
    console.error('Error saving product:', error)
    const errorMsg = error.response?.data?.errors 
      ? Object.values(error.response.data.errors).flat().join(', ')
      : error.response?.data?.message || 'حدث خطأ أثناء حفظ المنتج'
    toast.error(errorMsg)
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

const submitPackage = async () => {
  try {
    const data = {
      ...packageForm.value,
      photo: photoFile.value
    }

    if (isEditing.value) {
      await api.updatePackage(editingId.value, data)
      toast.success('تم تحديث الباقة بنجاح')
    } else {
      await api.createPackage(data)
      toast.success('تم إضافة الباقة بنجاح')
    }

    closeModal()
    fetchPackages()
  } catch (error) {
    console.error('Error saving package:', error)
    toast.error(isEditing.value ? 'فشل تحديث الباقة' : 'فشل إضافة الباقة')
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

// Package functions
const fetchPackages = async () => {
  try {
    const response = await api.getPackages()
    let allPackages = response.data.data || response.data
    
    // Filter by search
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      allPackages = allPackages.filter(pkg => 
        pkg.name_ar.toLowerCase().includes(query)
      )
    }
    
    // Filter by low stock
    if (showLowStock.value) {
      allPackages = allPackages.filter(pkg => pkg.quantity <= pkg.alert_quantity)
    }
    
    packages.value = allPackages
  } catch (error) {
    console.error('Error fetching packages:', error)
    toast.error('فشل تحميل الباقات')
  }
}

const editPackage = (pkg) => {
  isEditing.value = true
  editingId.value = pkg.id
  packageForm.value = {
    name_ar: pkg.name_ar,
    description: pkg.description || '',
    price_جملة: parseFloat(pkg.price_جملة),
    price_قطاعي: parseFloat(pkg.price_قطاعي),
    price_صفحة: parseFloat(pkg.price_صفحة),
    quantity: pkg.quantity,
    alert_quantity: pkg.alert_quantity
  }
  showModal.value = true
}

const deletePackage = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذه الباقة؟')) return
  
  try {
    await api.deletePackage(id)
    toast.success('تم حذف الباقة بنجاح')
    fetchPackages()
  } catch (error) {
    console.error('Error deleting package:', error)
    toast.error('فشل حذف الباقة')
  }
}

onMounted(() => {
  fetchProducts()
  fetchPackages()
})
</script>

<style scoped>
.stock-container {
  padding: 20px;
  direction: rtl;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h1 {
  font-size: 24px;
  color: #1e293b;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  align-items: center;
}

.search-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.badge-danger {
  background: #ef4444;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
}

.table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f8fafc;
  padding: 12px;
  text-align: right;
  font-weight: 600;
  color: #475569;
  border-bottom: 2px solid #e2e8f0;
}

td {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
}

.text-danger {
  color: #dc2626;
  font-weight: 600;
}

.text-success {
  color: #059669;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.ok {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.low {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.out {
  background: #fee2e2;
  color: #991b1b;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-icon {
  padding: 6px;
  background: #f1f5f9;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #e2e8f0;
}

.btn-primary,
.btn-secondary {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-large {
  max-width: 800px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #64748b;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #e2e8f0;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
  font-weight: 500;
  color: #475569;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
}

.selected-product {
  background: #f8fafc;
  padding: 15px;
  border-radius: 6px;
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.selected-product strong {
  font-size: 16px;
  color: #1e293b;
}

.selected-product span {
  font-size: 14px;
  color: #64748b;
}

.modal-large {
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.btn-success {
  background: #10b981;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.2s;
}

.btn-success:hover {
  background: #059669;
}

.bg-red-50 {
  background-color: #fef2f2;
}

.text-red-600 {
  color: #dc2626;
}

.text-green-600 {
  color: #059669;
}

.text-blue-600 {
  color: #2563eb;
}

.text-yellow-600 {
  color: #ca8a04;
}

.text-gray-500 {
  color: #6b7280;
}

.font-mono {
  font-family: ui-monospace, monospace;
}

.font-medium {
  font-weight: 500;
}

.font-semibold {
  font-weight: 600;
}

.font-bold {
  font-weight: 700;
}

.text-center {
  text-align: center;
}

.text-sm {
  font-size: 0.875rem;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #6b7280;
  font-size: 16px;
}

.price-preview {
  margin-top: 10px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
}

.price-item span {
  color: #64748b;
}

.price-item strong {
  font-size: 15px;
}

small {
  display: block;
  margin-top: 4px;
  font-size: 12px;
}

.price-input-wholesale {
  border-left: 3px solid #059669 !important;
}

.price-input-retail {
  border-left: 3px solid #2563eb !important;
}

.price-input-online {
  border-left: 3px solid #ca8a04 !important;
}

.file-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.photo-preview {
  margin-top: 10px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  max-width: 200px;
}

.photo-preview img {
  width: 100%;
  height: auto;
  display: block;
}
</style>

