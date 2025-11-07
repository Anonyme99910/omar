# Perfume Store Inventory System

## Database Setup Complete ✅

### Overview
- **Database**: `perfume_store`
- **Total Categories**: 6
- **Total Brands**: 8 (Premium Arabian brands)
- **Total Products**: 25 (Arabic perfume products)
- **Total Stock Value**: ~258,740 SAR

---

## Categories (6)

1. **Men Perfumes** (عطور رجالية)
2. **Women Perfumes** (عطور نسائية)
3. **Unisex Perfumes** (عطور للجنسين)
4. **Oud** (عود)
5. **Bakhoor** (بخور)
6. **Oils** (زيوت عطرية)

---

## Brands (8)

1. **Ajmal** (أجمل) - Premium Arabian perfumes
2. **Rasasi** (رصاصي) - Luxury Middle Eastern fragrances
3. **Al Haramain** (الحرمين) - Traditional Arabian scents
4. **Swiss Arabian** (سويس أرابيان) - Blend of Eastern and Western
5. **Lattafa** (لطافة) - Modern Arabian perfumes
6. **Nabeel** (نبيل) - Classic Arabian fragrances
7. **Abdul Samad Al Qurashi** (عبد الصمد القرشي) - Premium Oud
8. **Amouage** (أمواج) - Luxury Omani perfumes

---

## Sample Products (25 Total)

### Men's Perfumes
- **Dahn Al Oudh Moattaq** (دهن العود معتق) - Ajmal - 250 SAR
- **Hawas** (هواس) - Rasasi - 150 SAR
- **Junoon Noir** (جنون نوار) - Rasasi - 130 SAR
- **Shaghaf Oud** (شغف عود) - Swiss Arabian - 170 SAR
- **Interlude Man** (إنترلود رجالي) - Amouage - 650 SAR
- **Attar Al Ghutra** (عطر الغترة) - Swiss Arabian - 140 SAR

### Women's Perfumes
- **Wisal Dhahab** (وصال ذهب) - Ajmal - 180 SAR
- **Amber Oud Rouge** (عنبر عود روج) - Al Haramain - 220 SAR
- **Opulent Musk** (مسك فاخر) - Lattafa - 110 SAR
- **Raghba** (رغبة) - Lattafa - 100 SAR
- **Reflection Woman** (ريفليكشن نسائي) - Amouage - 620 SAR

### Unisex
- **Musk Al Ghazal** (مسك الغزال) - Nabeel - 85 SAR
- **Layali** (ليالي) - Swiss Arabian - 130 SAR
- **Fakhar** (فخر) - Lattafa - 120 SAR

### Oud Products
- **Royal Dehn Al Oud** (دهن العود الملكي) - Abdul Samad Al Qurashi - 350 SAR
- **Cambodian Oud** (عود كمبودي) - Abdul Samad Al Qurashi - 500 SAR
- **Oud Muattar** (عود معطر) - Al Haramain - 150 SAR
- **Areej Al Oud** (عريج العود) - Ajmal - 320 SAR

### Bakhoor (Incense)
- **Bakhoor Nasaem** (بخور نسائم) - Nabeel - 65 SAR
- **Bakhoor Oud Al Madina** (بخور عود المدينة) - Abdul Samad Al Qurashi - 110 SAR
- **Bakhoor Maamoul** (بخور معمول) - Al Haramain - 75 SAR

### Perfume Oils
- **Musk Oil** (زيت المسك) - Ajmal - 50 SAR
- **Amber Oil** (زيت العنبر) - Nabeel - 60 SAR
- **Sandalwood Oil** (زيت الصندل) - Abdul Samad Al Qurashi - 95 SAR
- **Rose Oil** (زيت الورد) - Lattafa - 70 SAR

---

## API Endpoints

### Public (No Auth Required)
- `GET /api` - API info
- `GET /api/categories` - List all categories
- `GET /api/brands` - List all brands
- `GET /api/products` - List all products

### Protected (Requires Auth)
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Current user

#### Products (Full CRUD)
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/barcode/{barcode}` - Search by barcode
- `GET /api/products/low-stock/list` - Low stock items
- `POST /api/products/{id}/adjust-stock` - Adjust stock

#### Inventory Management
- `GET /api/reports/inventory` - Inventory report
- `GET /api/reports/products` - Product report
- `GET /api/reports/dashboard` - Dashboard stats

---

## Test URLs

1. **API Root**: http://localhost/parfumes/backend/public/api
2. **Categories**: http://localhost/parfumes/backend/public/api/categories
3. **Brands**: http://localhost/parfumes/backend/public/api/brands
4. **Products**: http://localhost/parfumes/backend/public/api/products

---

## Admin Credentials

- **Email**: admin@perfume.com
- **Password**: password

---

## Database Schema

### Products Table
- `id` - Primary key
- `name` - English name
- `name_ar` - Arabic name
- `description` - Product description
- `barcode` - Unique barcode
- `sku` - Stock keeping unit
- `category_id` - Foreign key to categories
- `brand_id` - Foreign key to brands
- `cost_price` - Purchase price
- `selling_price` - Retail price
- `stock_quantity` - Current stock
- `min_stock_level` - Minimum stock alert
- `size` - Product size (ml/g)
- `image` - Main product image
- `images` - Additional images (JSON)
- `is_active` - Active status
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

### Inventory Movements Table
- Tracks all stock changes (sales, adjustments, returns)
- Links to products and users
- Records quantity changes and reasons

---

## Stock Management Features

✅ **Real-time inventory tracking**
✅ **Low stock alerts** (min_stock_level)
✅ **Barcode scanning support**
✅ **Multi-language** (English/Arabic)
✅ **Category & brand organization**
✅ **Cost & profit tracking**
✅ **Stock adjustment history**
✅ **Comprehensive reporting**

---

## Next Steps

1. **Frontend Integration**: Connect Vue.js frontend to API
2. **Image Upload**: Add product images
3. **Barcode Scanner**: Integrate barcode scanning
4. **Reports**: Build inventory reports dashboard
5. **Sales Module**: Complete sales and POS functionality

---

## Commands Reference

```bash
# Seed database
php artisan db:seed --force

# Refresh database (WARNING: Deletes all data)
php artisan migrate:fresh --seed

# Check data
php verify_data.php

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

**Status**: ✅ Complete - Inventory system fully operational with 25 Arabic perfume products
