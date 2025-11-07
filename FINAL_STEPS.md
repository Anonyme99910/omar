# ✅ Products Restructure - FINAL STEPS

## Current Status:
- ✅ Database: Complete
- ✅ Backend: Complete
- ⏳ Frontend: Needs update

---

## IMPORTANT: The old StockList.vue has references to removed fields

The current file still uses:
- `category_id`, `brand_id` (removed)
- `stock_quantity` (renamed to `quantity`)
- `min_stock_level` (renamed to `alert_quantity`)
- `cost_price`, `barcode` (removed)

---

## Quick Fix Option 1: Manual Update

Open `frontend/src/views/Stock/StockList.vue` and replace these lines:

### Line 40-46 (Table Headers):
```vue
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
```

### Line 50-66 (Table Rows):
```vue
<tr v-for="product in products" :key="product.id" :class="{'bg-red-50': product.is_low_stock}">
  <td class="font-mono text-sm">{{ product.sku }}</td>
  <td class="font-medium">{{ product.name_ar }}</td>
  <td class="text-center">{{ product.volume_ml }} مل</td>
  <td class="text-green-600 font-semibold">{{ formatPrice(product.price_جملة) }}</td>
  <td class="text-blue-600 font-semibold">{{ formatPrice(product.price_قطاعي) }}</td>
  <td class="text-yellow-600 font-semibold">{{ formatPrice(product.price_صفحة) }}</td>
  <td>
    <span :class="product.is_low_stock ? 'text-red-600 font-bold' : ''">
      {{ product.quantity }}
    </span>
  </td>
  <td class="text-gray-500">{{ product.alert_quantity }}</td>
  <td>
    <span v-if="product.is_low_stock" class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">منخفض</span>
    <span v-else class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">متوفر</span>
  </td>
  <td>
    <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-800">
      <Edit :size="18" />
    </button>
  </td>
</tr>
```

### Update loadStock function (around line 303):
```javascript
const loadStock = async () => {
  try {
    const params = {
      search: filters.value.search || undefined,
      low_stock: filters.value.lowStock || undefined
    }
    const response = await api.getProducts(params)  // Changed from getStock
    products.value = response.data.data || response.data
  } catch (error) {
    toast.error('فشل تحميل المخزون')
  }
}
```

---

## Quick Fix Option 2: Use Simplified Version

The complete new StockList.vue is in `PRODUCTS_COMPLETE_SUMMARY.md`

Copy the entire `<template>`, `<script>`, and `<style>` sections from that file.

---

## After Updating Frontend:

```bash
cd c:\xampp\htdocs\parfumes\frontend
npm run build
```

---

## Test Checklist:

1. [ ] Open المخزون page
2. [ ] See products with segment prices
3. [ ] Search works
4. [ ] Low stock filter works
5. [ ] Add product works
6. [ ] Edit product works
7. [ ] Segment prices auto-calculate

---

## If You Get Errors:

Check console for:
- API endpoint errors → Backend issue
- Missing fields → Frontend/Backend mismatch
- 500 errors → Check PHP error log

**The backend is 100% ready. Just need to update the frontend!**
