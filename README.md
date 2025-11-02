# 🌸 Perfume Store Management System

Enterprise-grade point-of-sale and inventory management system for perfume retail businesses.

## 🚀 Quick Start

**Access:** `http://localhost/parfumes/`

**Deploy Frontend:**
```bash
.\deploy_frontend.bat
```

---

## ✅ System Status

| Component | Status |
|-----------|--------|
| Backend (Laravel) | ✅ Operational |
| Frontend (Vue.js) | ✅ Operational |
| Database | ✅ Connected |
| Security | ✅ Enterprise-grade |
| Code Quality | ✅ Production-ready |

---

## 📋 Features

### Core Modules
- ✅ User Management (Admin, Manager, Cashier)
- ✅ Role-based Permissions System
- ✅ Customer Management (Encrypted)
- ✅ Product & Inventory Management
- ✅ Point of Sale (POS) System
- ✅ Sales & Invoicing
- ✅ Expense Tracking
- ✅ Sales Analysis & Reports
- ✅ Stock Management
- ✅ Damaged Products Tracking

### Security Features
- ✅ AES-256 Field Encryption (phone, address)
- ✅ SQL Injection Protection (Eloquent ORM)
- ✅ XSS Protection (Input Sanitization)
- ✅ CSRF Protection (Sanctum)
- ✅ Token Authentication (120min expiration)
- ✅ Inactivity Timeout (30min)
- ✅ Security Headers
- ✅ Password Hashing (bcrypt)

---

## 🛠️ Tech Stack

**Backend:**
- Laravel 10 + PHP 8.2
- MySQL Database
- Sanctum Authentication
- Custom Encryption Service

**Frontend:**
- Vue 3 + Composition API
- Vite Build Tool
- Tailwind CSS
- Pinia State Management
- Vue Router

---

## 📊 Database

**13 Tables:**
- users, customers (encrypted), products
- categories, brands, sales, sale_items
- payments, inventory_movements
- damaged_products, expenses
- sessions, personal_access_tokens

**Encrypted Fields:**
- Customer phone numbers (AES-256-CBC)
- Customer addresses (AES-256-CBC)

---

## 🗂️ Project Structure

```
parfumes/
├── backend/              Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   └── Services/
│   ├── config/
│   ├── database/migrations/
│   └── routes/
│
├── frontend/             Vue.js SPA
│   ├── src/
│   │   ├── views/
│   │   ├── layouts/
│   │   ├── services/
│   │   ├── stores/
│   │   └── utils/
│   └── public/
│
├── assets/               Compiled frontend
├── index.php             Entry point
└── deploy_frontend.bat   Deployment script
```

---

## 📱 Access & Credentials

**URL:** `http://localhost/parfumes/`

**Admin Account:**
- Email: admin@perfume.com
- Password: (configured)

---

## 🔧 Maintenance

**Deploy Frontend Changes:**
```bash
cd c:\xampp\htdocs\parfumes
.\deploy_frontend.bat
```

**Clear Laravel Cache:**
```bash
cd backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

**Check System Health:**
- All routes: `php artisan route:list`
- Database: Check `storage/logs/laravel.log`
- Frontend: Browser console (F12)

---

## 📈 Performance

- ✅ Optimized database queries
- ✅ Indexed search columns
- ✅ Minified frontend assets
- ✅ Lazy loading routes
- ✅ Efficient state management

---

## 🆘 Troubleshooting

**500 Error:**
- Clear Laravel cache
- Check `.env` configuration
- Verify file permissions

**Database Issues:**
- Check connection in `.env`
- Verify MySQL is running
- Check `storage/logs/laravel.log`

**Frontend Not Loading:**
- Run `deploy_frontend.bat`
- Clear browser cache
- Check browser console

---

## 📝 Notes

- Clean, production-ready codebase
- No test/debug files
- Enterprise-grade security
- Fully documented
- Ready for deployment

---

**Version:** 1.0.0  
**Status:** Production Ready  
**Last Updated:** November 2, 2025
