# Perfume Store Management System - Backend

نظام إدارة متجر عطور متكامل مبني باستخدام Laravel 10

## المتطلبات

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Laravel 10

## التثبيت

1. تثبيت الاعتماديات:
```bash
composer install
```

2. نسخ ملف البيئة:
```bash
copy .env.example .env
```

3. توليد مفتاح التطبيق:
```bash
php artisan key:generate
```

4. إعداد قاعدة البيانات في ملف `.env`:
```
DB_DATABASE=perfume_store
DB_USERNAME=root
DB_PASSWORD=
```

5. تشغيل الهجرات:
```bash
php artisan migrate
```

6. تشغيل البذور (اختياري):
```bash
php artisan db:seed
```

7. تشغيل السيرفر:
```bash
php artisan serve
```

## بيانات الدخول الافتراضية

- Email: admin@perfume.com
- Password: password

## API Endpoints

### Authentication
- POST `/api/login` - تسجيل الدخول
- POST `/api/register` - إنشاء حساب جديد
- POST `/api/logout` - تسجيل الخروج
- GET `/api/me` - معلومات المستخدم الحالي

### Categories
- GET `/api/categories` - جميع التصنيفات
- POST `/api/categories` - إضافة تصنيف
- GET `/api/categories/{id}` - تفاصيل تصنيف
- PUT `/api/categories/{id}` - تحديث تصنيف
- DELETE `/api/categories/{id}` - حذف تصنيف

### Brands
- GET `/api/brands` - جميع العلامات التجارية
- POST `/api/brands` - إضافة علامة تجارية
- GET `/api/brands/{id}` - تفاصيل علامة تجارية
- PUT `/api/brands/{id}` - تحديث علامة تجارية
- DELETE `/api/brands/{id}` - حذف علامة تجارية

### Products
- GET `/api/products` - جميع المنتجات
- POST `/api/products` - إضافة منتج
- GET `/api/products/{id}` - تفاصيل منتج
- PUT `/api/products/{id}` - تحديث منتج
- DELETE `/api/products/{id}` - حذف منتج
- GET `/api/products/barcode/{barcode}` - البحث بالباركود
- GET `/api/products/low-stock/list` - المنتجات منخفضة المخزون
- POST `/api/products/{id}/adjust-stock` - تعديل المخزون

### Customers
- GET `/api/customers` - جميع العملاء
- POST `/api/customers` - إضافة عميل
- GET `/api/customers/{id}` - تفاصيل عميل
- PUT `/api/customers/{id}` - تحديث عميل
- DELETE `/api/customers/{id}` - حذف عميل

### Sales
- GET `/api/sales` - جميع المبيعات
- POST `/api/sales` - إنشاء فاتورة
- GET `/api/sales/{id}` - تفاصيل فاتورة
- POST `/api/sales/{id}/cancel` - إلغاء فاتورة
- GET `/api/sales/today/summary` - ملخص مبيعات اليوم

### Reports
- GET `/api/reports/dashboard` - لوحة التحكم
- GET `/api/reports/sales` - تقرير المبيعات
- GET `/api/reports/products` - تقرير المنتجات
- GET `/api/reports/inventory` - تقرير المخزون
- GET `/api/reports/profit` - تقرير الأرباح

## الميزات

- ✅ إدارة المنتجات والتصنيفات والعلامات التجارية
- ✅ نظام الباركود
- ✅ إدارة المخزون مع التنبيهات
- ✅ نظام المبيعات والفواتير
- ✅ إدارة العملاء
- ✅ تقارير شاملة
- ✅ نظام المصادقة
- ✅ تتبع حركة المخزون

## الترخيص

MIT License
