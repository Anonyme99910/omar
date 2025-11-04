# 🚀 **PARFUMES - QUICK START GUIDE**

## ✅ **GET EVERYTHING WORKING IN 5 MINUTES!**

---

## 📋 **PREREQUISITES**

Make sure you have:
- ✅ XAMPP installed and running (Apache + MySQL)
- ✅ Composer installed
- ✅ Node.js & NPM installed
- ✅ Database `airbnb` exists in phpMyAdmin

---

## 🎯 **OPTION 1: AUTOMATIC SETUP (RECOMMENDED)**

### **Single Command:**
```bash
cd C:\xampp\htdocs\parfumes
.\COMPLETE_SETUP.bat
```

This will automatically:
1. Setup backend (.env, composer, migrations)
2. Create database tables
3. Create admin user
4. Setup mobile app
5. Setup admin panel
6. Test everything

---

## 🎯 **OPTION 2: MANUAL SETUP (IF AUTOMATIC FAILS)**

### **Step 1: Create Database Tables**

**Option A: Using phpMyAdmin**
1. Open: `http://localhost/phpmyadmin`
2. Select database: `airbnb`
3. Click "Import" tab
4. Choose file: `C:\xampp\htdocs\parfumes\database_setup.sql`
5. Click "Go"

**Option B: Using MySQL Command**
```bash
mysql -u root -p airbnb < C:\xampp\htdocs\parfumes\database_setup.sql
```

### **Step 2: Setup Backend**
```bash
cd C:\xampp\htdocs\parfumes\backend

# Copy .env
copy .env.example .env

# Install dependencies
composer install

# Generate key
php artisan key:generate

# Create storage link
php artisan storage:link
```

### **Step 3: Create Admin User (if not created by SQL)**
```bash
cd C:\xampp\htdocs\parfumes\backend
php artisan tinker

# In tinker:
App\Models\User::create([
    'email' => 'admin@parfumes.com',
    'password' => bcrypt('Admin@123'),
    'full_name' => 'Super Admin',
    'phone_number' => '12345678',
    'is_admin' => true,
    'is_active' => true
]);

# Press Ctrl+C to exit
```

### **Step 4: Setup Mobile App**
```bash
cd C:\xampp\htdocs\parfumes

# Install dependencies
npm install

# Create .env
echo EXPO_PUBLIC_API_URL=http://localhost/parfumes/backend/public/api > .env
```

### **Step 5: Setup Admin Panel**
```bash
cd C:\xampp\htdocs\parfumes\admin-panel

# Install dependencies
npm install

# Create .env
echo VITE_API_URL=http://localhost/parfumes/backend/public/api > .env

# Build (optional)
npm run build
```

---

## 🧪 **TESTING**

### **Test 1: Check Database**
1. Open: `http://localhost/phpmyadmin`
2. Select database: `airbnb`
3. You should see tables: `users`, `properties`, `favorites`, `sessions`, `password_reset_tokens`

### **Test 2: Check Backend API**
Open in browser:
```
http://localhost/parfumes/backend/public/api/properties
```

You should see JSON response (empty array is OK):
```json
{
  "data": [],
  "current_page": 1,
  "total": 0
}
```

### **Test 3: Test Admin Login**
Using curl or Postman:
```bash
curl -X POST http://localhost/parfumes/backend/public/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@parfumes.com\",\"password\":\"Admin@123\"}"
```

You should get a response with `token` and `user` data.

### **Test 4: Start Mobile App**
```bash
cd C:\xampp\htdocs\parfumes
npm run dev
```

Scan QR code with Expo Go app on your phone.

### **Test 5: Start Admin Panel**
```bash
cd C:\xampp\htdocs\parfumes\admin-panel
npm run dev
```

Open: `http://localhost:3000`
Login: admin@parfumes.com / Admin@123

---

## 🚨 **TROUBLESHOOTING**

### **Problem: "Could not open input file: artisan"**
**Solution:** The `artisan` file was created. Try again:
```bash
cd C:\xampp\htdocs\parfumes\backend
php artisan --version
```

### **Problem: "Database connection error"**
**Solution:** Check `.env` file in backend:
```env
DB_DATABASE=airbnb
DB_USERNAME=root
DB_PASSWORD=
```

### **Problem: "Table doesn't exist"**
**Solution:** Run the SQL file:
```bash
mysql -u root -p airbnb < database_setup.sql
```

Or import via phpMyAdmin.

### **Problem: "404 Not Found" for API**
**Solution:** Check `.htaccess` exists in `backend/public/`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /parfumes/backend/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

### **Problem: "CORS error"**
**Solution:** Already configured in `backend/config/cors.php`

### **Problem: "Admin user not created"**
**Solution:** Create manually:
```bash
cd backend
php artisan tinker
>>> App\Models\User::create(['email'=>'admin@parfumes.com','password'=>bcrypt('Admin@123'),'full_name'=>'Admin','phone_number'=>'12345678','is_admin'=>true]);
```

---

## 📊 **VERIFY EVERYTHING WORKS**

### **Checklist:**
- [ ] XAMPP Apache running (port 80)
- [ ] XAMPP MySQL running (port 3306)
- [ ] Database `airbnb` exists
- [ ] Tables created (users, properties, favorites, sessions, password_reset_tokens)
- [ ] Admin user exists (admin@parfumes.com)
- [ ] Backend API responds: `http://localhost/parfumes/backend/public/api/properties`
- [ ] Mobile app `.env` configured
- [ ] Admin panel `.env` configured
- [ ] Can login to admin panel

---

## 🎯 **WHAT TO DO NEXT**

### **1. Test Mobile App:**
```bash
cd C:\xampp\htdocs\parfumes
npm run dev
```

### **2. Test Admin Panel:**
```bash
cd C:\xampp\htdocs\parfumes\admin-panel
npm run dev
```

### **3. Create Test Data:**
- Register a user in mobile app
- Create some properties
- Login to admin panel
- Approve properties

---

## 📁 **IMPORTANT FILES**

```
C:\xampp\htdocs\parfumes\
├── backend\
│   ├── .env                    ← Configure this
│   ├── artisan                 ← Laravel command tool
│   └── public\.htaccess        ← Apache rules
│
├── admin-panel\
│   └── .env                    ← Configure this
│
├── .env                        ← Mobile app config
├── database_setup.sql          ← Run this in phpMyAdmin
├── COMPLETE_SETUP.bat          ← Automated setup
└── QUICK_START.md              ← This file
```

---

## 🔥 **QUICK COMMANDS**

```bash
# Setup everything
.\COMPLETE_SETUP.bat

# Start mobile app
npm run dev

# Start admin panel
cd admin-panel && npm run dev

# Check Laravel
cd backend && php artisan --version

# Create admin user
cd backend && php artisan tinker
>>> App\Models\User::create(['email'=>'admin@parfumes.com','password'=>bcrypt('Admin@123'),'full_name'=>'Admin','phone_number'=>'12345678','is_admin'=>true]);

# Test API
curl http://localhost/parfumes/backend/public/api/properties
```

---

## ✅ **SUCCESS INDICATORS**

You know everything is working when:
1. ✅ `http://localhost/parfumes/backend/public/api/properties` returns JSON
2. ✅ phpMyAdmin shows 5 tables in `airbnb` database
3. ✅ Admin login works: admin@parfumes.com / Admin@123
4. ✅ Mobile app connects and shows login screen
5. ✅ Admin panel shows login page

---

**🎉 Your system is ready! Start building!**
