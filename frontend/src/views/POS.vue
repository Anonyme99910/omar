<template>
  <!-- Customer Selection Modal (appears first) -->
  <div v-if="selectedCustomer === null" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="handleModalBackdropClick">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col relative">
      <!-- Navigation Menu Button -->
      <button
        @click="$router.back()"
        class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors z-10"
        title="القائمة"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Header - Fixed -->
      <div class="p-8 pb-4">
        <div class="text-center mb-6">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">مرحباً بك في نقطة البيع</h2>
          <p class="text-gray-600">اختر العميل للمتابعة</p>
        </div>

        <!-- Search Customer -->
        <div class="relative">
          <input
            v-model="customerSearchQuery"
            type="text"
            placeholder="ابحث عن عميل بالاسم أو الهاتف..."
            class="input pl-10 text-lg"
            autofocus
          />
          <Search class="absolute left-3 top-3.5 text-gray-400" :size="24" />
          <div v-if="customerSearchQuery" class="absolute right-3 top-3.5">
            <button @click="customerSearchQuery = ''" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Customer List - Scrollable -->
      <div class="flex-1 overflow-y-auto px-8 space-y-3">
        <!-- Walk-in Customer -->
        <button
          @click="selectCustomer(0, 'عميل عادي')"
          class="w-full p-4 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 rounded-xl text-right transition-all border-2 border-transparent hover:border-primary-500"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="font-bold text-lg text-gray-900">عميل عادي</p>
              <p class="text-sm text-gray-600">سعر قطاعي</p>
            </div>
            <div class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">
              قطاعي
            </div>
          </div>
        </button>

        <!-- Registered Customers -->
        <button
          v-for="customer in filteredCustomers"
          :key="customer.id"
          @click="selectCustomer(customer.id, customer.name)"
          class="w-full p-4 bg-white hover:bg-gray-50 rounded-xl text-right transition-all border-2 border-gray-200 hover:border-primary-500"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="font-bold text-lg text-gray-900">{{ customer.name }}</p>
              <p class="text-sm text-gray-600">{{ customer.phone }}</p>
            </div>
            <div 
              class="px-3 py-1 rounded-full text-sm font-medium"
              :class="{
                'bg-blue-100 text-blue-700': customer.segment === 'جملة',
                'bg-green-100 text-green-700': customer.segment === 'قطاعي',
                'bg-purple-100 text-purple-700': customer.segment === 'صفحة'
              }"
            >
              {{ customer.segment }}
            </div>
          </div>
        </button>
      </div>

      <!-- Add New Customer - Sticky Footer -->
      <div class="p-8 pt-4 border-t bg-white rounded-b-2xl">
        <button
          @click="openAddCustomerModal"
          class="w-full btn btn-primary flex items-center justify-center gap-2"
          type="button"
        >
          <UserPlus :size="20" />
          إضافة عميل جديد
        </button>
      </div>
    </div>
  </div>

  <!-- Packaging Modal (علبة) -->
  <div v-if="showPackagingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closePackagingModal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
      <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">إضافة علبة</h3>
      <p class="text-gray-600 mb-6 text-center">{{ selectedItemForPackaging?.name_ar }}</p>
      
      <div class="space-y-4">
        <!-- With or Without Packaging -->
        <div class="flex gap-3">
          <button
            @click="packagingOption = 'with'"
            :class="[
              'flex-1 py-3 px-4 rounded-lg font-medium transition-all border-2',
              packagingOption === 'with'
                ? 'bg-primary-600 text-white border-primary-600'
                : 'bg-white text-gray-700 border-gray-300 hover:border-primary-400'
            ]"
          >
            مع علبة
          </button>
          <button
            @click="packagingOption = 'without'"
            :class="[
              'flex-1 py-3 px-4 rounded-lg font-medium transition-all border-2',
              packagingOption === 'without'
                ? 'bg-primary-600 text-white border-primary-600'
                : 'bg-white text-gray-700 border-gray-300 hover:border-primary-400'
            ]"
          >
            بدون علبة
          </button>
        </div>

        <!-- Packaging Details (shown only if "with packaging") -->
        <div v-if="packagingOption === 'with'" class="space-y-4 p-4 bg-gray-50 rounded-lg">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">عدد العلب</label>
            <input
              v-model.number="packagingQuantity"
              type="number"
              min="1"
              class="input"
              placeholder="1"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">سعر العلبة</label>
            <input
              v-model.number="packagingPrice"
              type="number"
              step="0.01"
              min="0"
              class="input"
              placeholder="0.00"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 mt-6">
          <button
            @click="closePackagingModal"
            class="flex-1 py-3 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
          >
            إلغاء
          </button>
          <button
            @click="confirmAddToCart"
            class="flex-1 py-3 px-4 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors"
          >
            إضافة للسلة
          </button>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="flex flex-col lg:flex-row h-screen bg-gray-50">
    <!-- Products Section - Left Side (Full width on mobile, left side on desktop) -->
    <div class="flex-1 p-4 overflow-y-auto pb-44 lg:pb-4">
      <!-- Search -->
      <div class="card">
        <div class="relative">
          <input
            v-model="searchQuery"
            @input="handleProductSearch"
            type="text"
            :placeholder="viewMode === 'products' ? 'ابحث بالاسم أو الباركود أو الفئة...' : 'ابحث عن باقة...'"
            class="input pl-10 text-lg"
            autofocus
          />
          <Search class="absolute left-3 top-3.5 text-gray-400" :size="24" />
          <div v-if="isSearching" class="absolute left-12 top-3.5">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
          </div>
        </div>
      </div>

      <!-- View Mode Toggle -->
      <div class="flex justify-center">
        <div class="inline-flex rounded-lg border-2 border-primary-600 p-1 bg-white shadow-md">
          <button
            @click="switchViewMode('products')"
            :class="[
              'px-6 py-2 rounded-md font-medium transition-all',
              viewMode === 'products' 
                ? 'bg-primary-600 text-white shadow-md' 
                : 'text-gray-700 hover:bg-gray-100'
            ]"
          >
            المنتجات
          </button>
          <button
            @click="switchViewMode('packages')"
            :class="[
              'px-6 py-2 rounded-md font-medium transition-all',
              viewMode === 'packages' 
                ? 'bg-primary-600 text-white shadow-md' 
                : 'text-gray-700 hover:bg-gray-100'
            ]"
          >
            الباقات
          </button>
        </div>
      </div>

      <!-- Products/Packages Grid -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div
          v-for="item in displayItems"
          :key="item.id"
          @click="addToCart(item)"
          class="card hover:shadow-lg cursor-pointer transition-all hover:scale-105"
        >
          <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
            <img 
              v-if="item.photos && JSON.parse(item.photos)[0]" 
              :src="getPhotoUrl(item.photos)" 
              :alt="item.name_ar"
              class="w-full h-full object-cover"
            />
            <Package v-else :size="48" class="text-gray-400" />
          </div>
          <h4 class="font-bold text-gray-900 mb-1 truncate">{{ item.name_ar }}</h4>
          <p v-if="viewMode === 'products'" class="text-sm text-gray-500 mb-2">{{ formatVolume(item.volume_ml) }}</p>
          <p v-else class="text-sm text-gray-500 mb-2 line-clamp-2">{{ item.description || 'باقة' }}</p>
          <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-primary-600">{{ formatCurrencyLatin(getItemPrice(item)) }}</span>
            <span class="text-xs badge" :class="item.quantity > 0 ? 'badge-success' : 'badge-danger'">
              {{ toLatinNumbers(item.quantity) }} قطعة
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Section - Right Side (Hidden on mobile, shown on desktop) -->
    <div class="hidden lg:flex lg:w-96 bg-white border-l border-gray-200 flex-col">
      <div class="flex-1 p-4 overflow-y-auto">
        <!-- Selected Customer Info -->
        <div class="mb-4 p-3 bg-primary-50 rounded-lg border border-primary-200">
          <div class="flex items-center justify-between mb-2">
            <div>
              <p class="text-sm text-gray-600">العميل الحالي</p>
              <p class="font-bold text-gray-900">{{ selectedCustomerName }}</p>
            </div>
            <button
              @click="changeCustomer"
              class="text-sm text-primary-600 hover:text-primary-800 font-medium"
            >
              تغيير
            </button>
          </div>
          <div class="text-xs text-gray-600">
            السعر: {{ getCurrentSegmentLabel() }}
          </div>
        </div>

        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
          <ShoppingCart :size="24" />
          سلة المشتريات
        </h3>

        <!-- Cart Items -->
        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
          <div v-if="cart.length === 0" class="text-center py-8 text-gray-500">
            السلة فارغة
          </div>
          <div
            v-for="(item, index) in cart"
            :key="`${item.product.id}-${index}`"
            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex-1">
              <p class="font-medium text-sm">{{ item.product.name_ar }}</p>
              <p class="text-xs text-gray-500">{{ formatCurrencyLatin(item.unit_price) }}</p>
              <p v-if="item.with_packaging" class="text-xs text-primary-600 font-medium mt-1">
                📦 مع علبة ({{ toLatinNumbers(item.packaging_quantity) }} × {{ formatCurrencyLatin(item.packaging_price) }})
              </p>
            </div>
            <div class="flex items-center gap-2">
              <button @click="decreaseQuantity(item)" class="w-7 h-7 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                <Minus :size="16" />
              </button>
              <input
                :value="item.quantity"
                @input="updateQuantity(item, $event.target.value)"
                @blur="validateQuantity(item)"
                type="number"
                min="1"
                :max="item.product.quantity"
                class="w-12 text-center font-bold border rounded py-1 text-sm"
              />
              <button @click="increaseQuantity(item)" class="w-7 h-7 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                <Plus :size="16" />
              </button>
            </div>
            <button @click="removeFromCart(item)" class="text-red-600 hover:text-red-800">
              <Trash2 :size="18" />
            </button>
          </div>
        </div>

        <!-- Totals -->
        <div class="border-t pt-4 space-y-2">
          <div class="flex justify-between text-sm">
            <span>المجموع الفرعي:</span>
            <span class="font-bold">{{ formatCurrencyLatin(subtotal) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span>الخصم:</span>
            <input
              v-model.number="discount"
              type="number"
              step="0.01"
              class="input w-32 text-left py-1"
            />
          </div>
          <div class="flex justify-between text-sm">
            <span>الضريبة:</span>
            <input
              v-model.number="tax"
              type="number"
              step="0.01"
              class="input w-32 text-left py-1"
            />
          </div>
          <div class="flex justify-between text-lg font-bold border-t pt-2">
            <span>الإجمالي:</span>
            <span class="text-primary-600">{{ formatCurrencyLatin(total) }}</span>
          </div>
          <div class="flex justify-between text-sm bg-blue-50 p-2 rounded">
            <span>المبلغ المدفوع:</span>
            <input
              v-model.number="paidAmount"
              type="number"
              step="0.01"
              :placeholder="total.toString()"
              class="input w-32 text-left py-1"
            />
          </div>
          <div v-if="remainingAmount > 0" class="flex justify-between text-sm text-red-600 font-bold">
            <span>المتبقي:</span>
            <span>{{ formatCurrencyLatin(remainingAmount) }}</span>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">طريقة الدفع</label>
          <div class="grid grid-cols-2 gap-2">
            <button
              v-for="method in paymentMethods"
              :key="method.value"
              @click="paymentMethod = method.value"
              :class="paymentMethod === method.value ? 'bg-primary-600 text-white' : 'bg-gray-100'"
              class="py-2 rounded-lg font-medium transition-all"
            >
              {{ method.label }}
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 space-y-2">
          <button
            @click="completeSale"
            :disabled="cart.length === 0"
            class="btn btn-success w-full py-3 text-lg"
          >
            إتمام البيع
          </button>
          <button @click="clearCart" class="btn btn-secondary w-full">
            مسح السلة
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Sticky Bottom Button (Only visible on mobile) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-lg z-40">
      <!-- Customer Info Bar -->
      <div class="bg-primary-50 px-4 py-2 border-b border-primary-200">
        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span class="text-gray-600">العميل:</span>
            <span class="font-bold text-gray-900">{{ selectedCustomerName }}</span>
          </div>
          <button
            @click="changeCustomer"
            class="text-xs text-primary-600 hover:text-primary-800 font-medium px-2 py-1 bg-white rounded"
          >
            تغيير
          </button>
        </div>
        <div class="text-xs text-gray-600 mt-1">
          السعر: {{ getCurrentSegmentLabel() }}
        </div>
      </div>
      
      <!-- Cart Summary -->
      <div class="px-4 py-3">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-sm text-gray-600">إجمالي السلة</p>
            <p class="text-2xl font-bold text-primary-600">{{ formatCurrencyLatin(total) }}</p>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-600">عدد المنتجات</p>
            <p class="text-xl font-bold text-gray-900">{{ toLatinNumbers(cart.length) }}</p>
          </div>
        </div>
        <button
          @click="showMobileCart = true"
          :disabled="cart.length === 0"
          class="btn btn-success w-full py-4 text-lg font-bold flex items-center justify-center gap-2"
        >
          <ShoppingCart :size="24" />
          إتمام الطلب ({{ toLatinNumbers(cart.length) }})
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Cart Full Page (Full screen on mobile) -->
  <div v-if="showMobileCart" class="lg:hidden fixed inset-0 bg-gray-50 z-50 flex flex-col">
    <!-- Header with Back Button -->
    <div class="bg-primary-600 text-white p-4 flex items-center gap-4 shadow-lg">
      <button @click="showMobileCart = false" class="text-white hover:bg-primary-700 rounded-lg p-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <div class="flex-1">
        <h2 class="text-xl font-bold flex items-center gap-2">
          <ShoppingCart :size="24" />
          سلة المشتريات
        </h2>
      </div>
    </div>

    <!-- Customer Info -->
    <div class="p-4 bg-primary-50 border-b border-primary-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-600">العميل الحالي</p>
          <p class="font-bold text-gray-900">{{ selectedCustomerName }}</p>
        </div>
        <button
          @click="changeCustomer"
          class="text-sm text-primary-600 hover:text-primary-800 font-medium"
        >
          تغيير
        </button>
      </div>
      <div class="text-xs text-gray-600 mt-1">
        السعر: {{ getCurrentSegmentLabel() }}
      </div>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-4">
      <div v-if="cart.length === 0" class="text-center py-12 text-gray-500">
        السلة فارغة
      </div>
      <div v-else class="space-y-3">
        <div
          v-for="(item, index) in cart"
          :key="`${item.product.id}-${index}`"
          class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
        >
          <div class="flex-1">
            <p class="font-medium text-sm">{{ item.product.name_ar }}</p>
            <p class="text-xs text-gray-500">{{ formatCurrencyLatin(item.unit_price) }}</p>
            <p v-if="item.with_packaging" class="text-xs text-primary-600 font-medium mt-1">
              📦 مع علبة ({{ toLatinNumbers(item.packaging_quantity) }} × {{ formatCurrencyLatin(item.packaging_price) }})
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="decreaseQuantity(item)" class="w-8 h-8 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
              <Minus :size="16" />
            </button>
            <input
              :value="item.quantity"
              @input="updateQuantity(item, $event.target.value)"
              @blur="validateQuantity(item)"
              type="number"
              min="1"
              :max="item.product.quantity"
              class="w-14 text-center font-bold border rounded py-1 text-sm"
            />
            <button @click="increaseQuantity(item)" class="w-8 h-8 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
              <Plus :size="16" />
            </button>
          </div>
          <button @click="removeFromCart(item)" class="text-red-600 hover:text-red-800">
            <Trash2 :size="18" />
          </button>
        </div>
      </div>
    </div>

    <!-- Totals and Payment Details -->
    <div class="border-t bg-white">
      <div class="p-4 space-y-3">
        <!-- Subtotal -->
        <div class="flex justify-between items-center text-sm">
          <span class="text-gray-600">المجموع الفرعي:</span>
          <span class="font-bold text-gray-900">{{ formatCurrencyLatin(subtotal) }}</span>
        </div>

        <!-- Discount -->
        <div class="flex justify-between items-center text-sm">
          <label class="text-gray-600">الخصم:</label>
          <input
            v-model.number="discount"
            type="number"
            step="0.01"
            min="0"
            class="input w-32 text-left py-2 text-sm"
            placeholder="0.00"
          />
        </div>

        <!-- Tax -->
        <div class="flex justify-between items-center text-sm">
          <label class="text-gray-600">الضريبة:</label>
          <input
            v-model.number="tax"
            type="number"
            step="0.01"
            min="0"
            class="input w-32 text-left py-2 text-sm"
            placeholder="0.00"
          />
        </div>

        <!-- Total -->
        <div class="flex justify-between items-center text-lg font-bold text-primary-600 pt-3 border-t-2 border-gray-200">
          <span>الإجمالي:</span>
          <span>{{ formatCurrencyLatin(total) }}</span>
        </div>

        <!-- Payment Method -->
        <div class="pt-3 border-t border-gray-200">
          <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع:</label>
          <div class="grid grid-cols-2 gap-2">
            <button
              v-for="method in paymentMethods"
              :key="method.value"
              @click="paymentMethod = method.value"
              :class="[
                'py-3 px-4 rounded-lg font-medium transition-all border-2',
                paymentMethod === method.value
                  ? 'bg-primary-600 text-white border-primary-600'
                  : 'bg-white text-gray-700 border-gray-300 hover:border-primary-400'
              ]"
            >
              {{ method.label }}
            </button>
          </div>
        </div>

        <!-- Paid Amount -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ المدفوع:</label>
          <input
            v-model.number="paidAmount"
            type="number"
            step="0.01"
            min="0"
            class="input w-full text-left py-2"
            :placeholder="formatCurrencyLatin(total)"
          />
          <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للدفع الكامل</p>
        </div>

        <!-- Remaining Amount (if partial payment) -->
        <div v-if="paidAmount && paidAmount < total" class="bg-red-50 border border-red-200 rounded-lg p-3">
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-red-800">المبلغ المتبقي:</span>
            <span class="text-lg font-bold text-red-600">{{ formatCurrencyLatin(remainingAmount) }}</span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="p-4 pt-0 space-y-2">
        <button
          @click="completeSale(); showMobileCart = false"
          :disabled="cart.length === 0"
          class="btn btn-success w-full py-4 text-lg font-bold"
        >
          إتمام البيع
        </button>
        <button @click="clearCart" class="btn btn-secondary w-full py-3">
          مسح السلة
        </button>
      </div>
    </div>
  </div>

  <!-- Add Customer Modal (outside both customer selection and main POS) -->
  <div v-if="showCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]" @click.self="showCustomerModal = false">
    <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
      <!-- Close Button -->
      <button
        @click="showCustomerModal = false"
        class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors"
        title="إغلاق"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <h3 class="text-2xl font-bold mb-4">إضافة عميل جديد</h3>
      <form @submit.prevent="addCustomer" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">الاسم *</label>
          <input 
            v-model="customerForm.name" 
            type="text" 
            required 
            class="input" 
            placeholder="أحمد محمد"
            autofocus
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">رقم الهاتف *</label>
          <input 
            v-model="customerForm.phone" 
            type="tel" 
            required 
            class="input" 
            placeholder="01012345678"
            pattern="[0-9]{11}"
            title="الرجاء إدخال رقم هاتف صحيح (11 رقم)"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">الشريحة</label>
          <select v-model="customerForm.segment" class="input">
            <option value="قطاعي">قطاعي</option>
            <option value="جملة">جملة</option>
            <option value="صفحة">صفحة</option>
          </select>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showCustomerModal = false" class="btn btn-secondary flex-1">إلغاء</button>
          <button type="submit" class="btn btn-primary flex-1">حفظ وإضافة</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Search, ShoppingCart, Plus, Minus, Trash2, Package, UserPlus } from 'lucide-vue-next'
import api from '@/services/api'
import { useToast } from 'vue-toastification'
import { toLatinNumbers, formatCurrencyLatin } from '@/utils/numbers'

const router = useRouter()
const toast = useToast()

const viewMode = ref('products') // 'products' or 'packages'
const products = ref([])
const allProducts = ref([])
const packages = ref([])
const allPackages = ref([])
const customers = ref([])
const cart = ref([])
const searchQuery = ref('')
const customerSearchQuery = ref('')
const selectedCustomer = ref(null)
const selectedCustomerName = ref('')

const discount = ref(0)
const tax = ref(0)
const paidAmount = ref(null)
const paymentMethod = ref('cash')
const showCustomerModal = ref(false)
const showCustomerDropdown = ref(false)
const isSearching = ref(false)
let searchTimeout = null

// Packaging modal state
const showPackagingModal = ref(false)
const selectedItemForPackaging = ref(null)
const packagingOption = ref('without')
const packagingQuantity = ref(1)
const packagingPrice = ref(0)

// Mobile cart modal state
const showMobileCart = ref(false)

const customerForm = ref({
  name: '',
  phone: '',
  segment: 'قطاعي'
})

const paymentMethods = [
  { value: 'cash', label: 'نقدي' },
  { value: 'transfer', label: 'تحويل' }
]

const displayItems = computed(() => {
  const items = viewMode.value === 'products' ? products.value : packages.value
  if (!searchQuery.value) return items.slice(0, 12)
  return items
})

const filteredCustomers = computed(() => {
  if (!customerSearchQuery.value) return customers.value
  
  const query = customerSearchQuery.value.toLowerCase().trim()
  
  // Smart search: normalize Arabic characters and remove diacritics
  const normalizeArabic = (text) => {
    return text
      .replace(/[أإآ]/g, 'ا')
      .replace(/[ى]/g, 'ي')
      .replace(/[ة]/g, 'ه')
      .replace(/[\u064B-\u065F]/g, '') // Remove diacritics
  }
  
  const normalizedQuery = normalizeArabic(query)
  
  return customers.value
    .map(customer => {
      const name = normalizeArabic(customer.name.toLowerCase())
      const phone = customer.phone.toString()
      let score = 0
      
      // Exact match (highest priority)
      if (name === normalizedQuery || phone === query) {
        score = 1000
      }
      // Starts with query
      else if (name.startsWith(normalizedQuery) || phone.startsWith(query)) {
        score = 500
      }
      // Contains query
      else if (name.includes(normalizedQuery) || phone.includes(query)) {
        score = 100
      }
      // Fuzzy match - all characters appear in order
      else {
        let queryIndex = 0
        for (let i = 0; i < name.length && queryIndex < normalizedQuery.length; i++) {
          if (name[i] === normalizedQuery[queryIndex]) {
            queryIndex++
            score += 1
          }
        }
        if (queryIndex !== normalizedQuery.length) score = 0
      }
      
      return { customer, score }
    })
    .filter(item => item.score > 0)
    .sort((a, b) => b.score - a.score)
    .slice(0, 10)
    .map(item => item.customer)
})

const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => {
    const productTotal = item.unit_price * item.quantity
    const packagingTotal = item.with_packaging ? (item.packaging_price * item.packaging_quantity) : 0
    return sum + productTotal + packagingTotal
  }, 0)
})

const total = computed(() => {
  return subtotal.value + tax.value - discount.value
})

const remainingAmount = computed(() => {
  const paid = paidAmount.value || total.value
  return Math.max(0, total.value - paid)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('ar-EG', {
    style: 'currency',
    currency: 'EGP'
  }).format(value)
}

const handleProductSearch = () => {
  // Clear previous timeout
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  // Show loading indicator
  isSearching.value = true
  
  // Debounce search - wait 300ms after user stops typing
  searchTimeout = setTimeout(async () => {
    if (viewMode.value === 'products') {
      await searchProducts()
    } else {
      await searchPackages()
    }
    isSearching.value = false
  }, 300)
}

const searchProducts = async () => {
  const query = searchQuery.value.trim()
  
  // If empty, show initial products
  if (!query) {
    products.value = allProducts.value.slice(0, 12)
    return
  }
  
  // Client-side fuzzy search for instant results
  const lowerQuery = query.toLowerCase()
  
  const filtered = allProducts.value.filter(p => {
    const name = p.name_ar.toLowerCase()
    const barcode = p.barcode?.toLowerCase() || ''
    const category = p.category?.name_ar?.toLowerCase() || ''
    const brand = p.brand?.name_ar?.toLowerCase() || ''
    
    // Exact match
    if (name.includes(lowerQuery) || 
        barcode.includes(lowerQuery) ||
        category.includes(lowerQuery) ||
        brand.includes(lowerQuery)) {
      return true
    }
    
    // Fuzzy match - check if all characters appear in order
    let queryIndex = 0
    for (let i = 0; i < name.length && queryIndex < lowerQuery.length; i++) {
      if (name[i] === lowerQuery[queryIndex]) {
        queryIndex++
      }
    }
    return queryIndex === lowerQuery.length
  })
  
  products.value = filtered
  
  // If no results locally and query is long enough, try server search
  if (filtered.length === 0 && query.length >= 2) {
    try {
      const response = await api.getProducts({ search: query })
      products.value = response.data.data || response.data || []
    } catch (error) {
      console.error('Search error:', error)
    }
  }
}

const searchPackages = async () => {
  const query = searchQuery.value.trim()
  
  // If empty, show initial packages
  if (!query) {
    packages.value = allPackages.value.slice(0, 12)
    return
  }
  
  // Client-side search
  const lowerQuery = query.toLowerCase()
  
  const filtered = allPackages.value.filter(pkg => {
    const name = pkg.name_ar.toLowerCase()
    const description = pkg.description?.toLowerCase() || ''
    
    return name.includes(lowerQuery) || description.includes(lowerQuery)
  })
  
  packages.value = filtered
}

const handleCustomerSearch = () => {
  // Just for filtering in the modal
}

const getPhotoUrl = (photos) => {
  if (!photos) return ''
  const photoArray = JSON.parse(photos)
  const photoUrl = photoArray[0]
  
  // If it's a data URL, return as is
  if (photoUrl.startsWith('data:')) {
    return photoUrl
  }
  
  // Otherwise, prepend the backend URL
  return `http://localhost/parfumes/backend/public${photoUrl}`
}

// Ensure volume shows with unit 'mL' when missing
const formatVolume = (volume) => {
  if (!volume && volume !== 0) return ''
  const v = String(volume).trim()
  // If already contains a unit (ml, mL, ML, or Arabic 'مل'), return as-is
  if (/\bml\b/i.test(v) || v.includes('مل')) return v
  return `${v} مل`
}

const getProductPrice = (product) => {
  // Get price based on customer segment
  const customer = customers.value.find(c => c.id === selectedCustomer.value)
  const segment = customer?.segment || 'قطاعي'
  
  if (segment === 'جملة') {
    return parseFloat(product.price_جملة)
  } else if (segment === 'صفحة') {
    return parseFloat(product.price_صفحة)
  } else {
    return parseFloat(product.price_قطاعي)
  }
}

const addToCart = (item) => {
  if (item.quantity === 0) {
    toast.error(viewMode.value === 'products' ? 'المنتج غير متوفر في المخزون' : 'الباقة غير متوفرة في المخزون')
    return
  }

  // Show packaging modal
  selectedItemForPackaging.value = item
  packagingOption.value = 'without'
  packagingQuantity.value = 1
  packagingPrice.value = 0
  showPackagingModal.value = true
}

const closePackagingModal = () => {
  showPackagingModal.value = false
  selectedItemForPackaging.value = null
  packagingOption.value = 'without'
  packagingQuantity.value = 1
  packagingPrice.value = 0
}

const confirmAddToCart = () => {
  const item = selectedItemForPackaging.value
  if (!item) return

  const existingItem = cart.value.find(cartItem => 
    cartItem.product.id === item.id && 
    cartItem.with_packaging === (packagingOption.value === 'with') &&
    cartItem.packaging_quantity === (packagingOption.value === 'with' ? packagingQuantity.value : null) &&
    cartItem.packaging_price === (packagingOption.value === 'with' ? packagingPrice.value : null)
  )
  
  if (existingItem) {
    if (existingItem.quantity >= item.quantity) {
      toast.error('الكمية المطلوبة غير متوفرة')
      closePackagingModal()
      return
    }
    existingItem.quantity++
  } else {
    const cartItem = {
      product: item,
      quantity: 1,
      unit_price: getItemPrice(item),
      with_packaging: packagingOption.value === 'with',
      packaging_quantity: packagingOption.value === 'with' ? packagingQuantity.value : null,
      packaging_price: packagingOption.value === 'with' ? packagingPrice.value : null
    }
    cart.value.push(cartItem)
  }
  
  toast.success(viewMode.value === 'products' ? 'تم إضافة المنتج للسلة' : 'تم إضافة الباقة للسلة')
  closePackagingModal()
}

const increaseQuantity = (item) => {
  if (item.quantity >= item.product.quantity) {
    toast.error('الكمية المطلوبة غير متوفرة')
    return
  }
  item.quantity++
}

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity--
  } else {
    removeFromCart(item)
  }
}

const updateQuantity = (item, newQuantity) => {
  const quantity = parseInt(newQuantity) || 1
  if (quantity < 1) {
    item.quantity = 1
    return
  }
  if (quantity > item.product.quantity) {
    toast.error('الكمية المطلوبة غير متوفرة')
    item.quantity = item.product.quantity
    return
  }
  item.quantity = quantity
}

const validateQuantity = (item) => {
  if (item.quantity < 1) {
    item.quantity = 1
  } else if (item.quantity > item.product.quantity) {
    item.quantity = item.product.quantity
    toast.error('الكمية المطلوبة غير متوفرة')
  }
}

const removeFromCart = (item) => {
  cart.value = cart.value.filter(i => i.product.id !== item.product.id)
}

const clearCart = () => {
  if (cart.value.length === 0) return
  if (confirm('هل أنت متأكد من مسح السلة؟')) {
    cart.value = []
    discount.value = 0
    tax.value = 0
  }
}

const addCustomer = async () => {
  if (!customerForm.value.name || !customerForm.value.phone) {
    toast.error('الرجاء إدخال الاسم ورقم الهاتف')
    return
  }

  try {
    const response = await api.createCustomer(customerForm.value)
    const newCustomer = response.data.data || response.data
    
    toast.success('تم إضافة العميل بنجاح')
    
    // Add to customers list
    customers.value.unshift(newCustomer)
    
    // Auto-select the new customer
    selectedCustomer.value = newCustomer.id
    selectedCustomerName.value = newCustomer.name
    
    // Close modal and reset form
    showCustomerModal.value = false
    customerForm.value = { name: '', phone: '', segment: 'قطاعي' }
    
    toast.info(`تم اختيار: ${newCustomer.name}`)
  } catch (error) {
    console.error('Error adding customer:', error)
    toast.error(error.response?.data?.message || 'فشل إضافة العميل')
  }
}

const completeSale = async () => {
  if (cart.value.length === 0) return

  const saleData = {
    customer_id: selectedCustomer.value === 0 ? null : selectedCustomer.value,
    items: cart.value.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity,
      unit_price: item.unit_price,
      with_packaging: item.with_packaging || false,
      packaging_quantity: item.packaging_quantity || null,
      packaging_price: item.packaging_price || null
    })),
    tax: tax.value,
    discount: discount.value,
    paid_amount: paidAmount.value || total.value,
    payment_method: paymentMethod.value
  }

  try {
    const response = await api.createSale(saleData)
    const invoiceNum = toLatinNumbers(response.data.invoice_number)
    toast.success(`تم إنشاء الفاتورة ${invoiceNum}`)
    
    const actions = []
    if (confirm('هل تريد تحميل الفاتورة PDF؟')) {
      try {
        await api.downloadInvoicePdf(response.data.id)
        toast.success('تم تحميل الفاتورة بنجاح')
      } catch (error) {
        console.error('PDF Error:', error)
        toast.error('فشل تحميل الفاتورة PDF')
      }
    }
    
    if (response.data.customer?.phone && confirm('هل تريد مشاركة الفاتورة عبر واتساب؟')) {
      shareViaWhatsApp(response.data)
    }
    
    // Clear cart but keep customer selected
    cart.value = []
    discount.value = 0
    tax.value = 0
    paidAmount.value = null
    
    // Refresh products
    fetchProducts()
  } catch (error) {
    toast.error(error.response?.data?.error || 'فشل إنشاء الفاتورة')
  }
}

const shareViaWhatsApp = async (sale) => {
  try {
    // Get WhatsApp message (already includes the public PDF link) and phone from backend
    const response = await api.getWhatsAppMessage(sale.id)
    const { message, phone } = response.data
    
    if (!phone) {
      toast.error('لا يوجد رقم هاتف للعميل')
      return
    }
    
    // Clean phone number (remove any non-digit characters)
    const cleanPhone = phone.replace(/[^0-9]/g, '')
    
    // Open WhatsApp with minimal pre-filled message from backend
    const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`
    window.open(whatsappUrl, '_blank')
    
    toast.success('تم فتح واتساب بنجاح')
  } catch (error) {
    console.error('WhatsApp Error:', error)
    console.error('Error details:', error.response?.data)
    toast.error('فشل مشاركة الفاتورة عبر واتساب: ' + (error.response?.data?.message || error.message))
  }
}

const fetchProducts = async () => {
  try {
    const response = await api.getProducts()
    const data = response.data.data || response.data || []
    allProducts.value = data
    products.value = data  // Show ALL products, not just 12
  } catch (error) {
    console.error('Failed to load products:', error)
    allProducts.value = []
    products.value = []
  }
}

const fetchCustomers = async () => {
  console.log('📥 Fetching customers...')
  try {
    const response = await api.getCustomers()
    customers.value = response.data.data || response.data || []
    console.log('✅ Customers loaded:', customers.value.length)
  } catch (error) {
    console.error('❌ Failed to load customers:', error)
    customers.value = []
  }
}

const fetchPackages = async () => {
  try {
    const response = await api.getPackages()
    const data = response.data.data || response.data || []
    allPackages.value = data
    packages.value = data
  } catch (error) {
    console.error('Failed to load packages:', error)
    allPackages.value = []
    packages.value = []
  }
}

const switchViewMode = (mode) => {
  viewMode.value = mode
  searchQuery.value = ''
  if (mode === 'packages' && packages.value.length === 0) {
    fetchPackages()
  }
}

const getItemPrice = (item) => {
  return viewMode.value === 'products' ? getProductPrice(item) : getPackagePrice(item)
}

const getPackagePrice = (pkg) => {
  const customer = customers.value.find(c => c.id === selectedCustomer.value)
  const segment = customer?.segment || 'قطاعي'
  
  if (segment === 'جملة') {
    return parseFloat(pkg.price_جملة)
  } else if (segment === 'صفحة') {
    return parseFloat(pkg.price_صفحة)
  } else {
    return parseFloat(pkg.price_قطاعي)
  }
}

// Handle modal backdrop click (go back to previous page)
const handleModalBackdropClick = () => {
  router.back()
}

// Open add customer modal
const openAddCustomerModal = () => {
  showCustomerModal.value = true
}

// Select customer and proceed to POS
const selectCustomer = (customerId, customerName) => {
  selectedCustomer.value = customerId
  selectedCustomerName.value = customerName
  customerSearchQuery.value = ''
  toast.success(`تم اختيار: ${customerName}`)
}

// Change customer (clears cart for safety)
const changeCustomer = () => {
  if (cart.value.length > 0) {
    if (!confirm('سيتم مسح السلة الحالية. هل تريد المتابعة؟')) {
      return
    }
  }
  cart.value = []
  selectedCustomer.value = null
  selectedCustomerName.value = ''
  customerSearchQuery.value = ''
}

// Get current segment label
const getCurrentSegmentLabel = () => {
  if (selectedCustomer.value === 0 || selectedCustomer.value === null) return 'قطاعي'
  const customer = customers.value.find(c => c.id === selectedCustomer.value)
  return customer?.segment || 'قطاعي'
}

onMounted(() => {
  fetchProducts()
  fetchCustomers()
})
</script>
