# 🔐 Parfumes Admin Panel - Vue.js + Tailwind CSS

## 🚀 **Complete Super Admin Panel**

Built with Vue.js 3, Tailwind CSS, and Laravel backend.

---

## 📦 **Installation**

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

---

## 🎯 **Features**

### **✅ Dashboard**
- Total statistics (users, properties, pending approvals)
- Recent users and properties
- Charts (properties by category & status)
- Real-time data

### **✅ User Management**
- View all users with pagination
- Search by name, email, phone
- Filter by active/inactive status
- Activate/deactivate users
- View user's properties count

### **✅ Property Management**
- View all properties with pagination
- Filter by status (pending/approved/rejected)
- Filter by category
- Search by title, location
- Approve/reject properties
- Delete properties
- View property details with images

### **✅ Authentication**
- Secure admin login
- Token-based authentication
- Auto-redirect on unauthorized access
- Session management

---

## 🔌 **API Endpoints Used**

```
POST   /api/login
POST   /api/logout
GET    /api/user
GET    /api/admin/dashboard
GET    /api/admin/statistics/category
GET    /api/admin/statistics/status
GET    /api/admin/users
PUT    /api/admin/users/{id}/toggle-status
GET    /api/admin/properties
PUT    /api/admin/properties/{id}/status
DELETE /api/admin/properties/{id}
```

---

## 📁 **Project Structure**

```
admin-panel/
├── src/
│   ├── components/
│   │   ├── Sidebar.vue           # Navigation sidebar
│   │   ├── Header.vue            # Top header
│   │   ├── StatCard.vue          # Statistics card
│   │   ├── UserTable.vue         # Users table
│   │   ├── PropertyTable.vue     # Properties table
│   │   └── PropertyModal.vue     # Property details modal
│   │
│   ├── views/
│   │   ├── Login.vue             # Login page
│   │   ├── Dashboard.vue         # Dashboard page
│   │   ├── Users.vue             # Users management
│   │   └── Properties.vue        # Properties management
│   │
│   ├── stores/
│   │   └── auth.js               # Auth store (Pinia)
│   │
│   ├── services/
│   │   └── api.js                # API client
│   │
│   ├── router/
│   │   └── index.js              # Vue Router
│   │
│   ├── App.vue                   # Root component
│   ├── main.js                   # Entry point
│   └── style.css                 # Tailwind CSS
│
├── index.html
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

---

## 🎨 **Tech Stack**

- **Vue.js 3** - Progressive JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Next generation frontend tooling
- **Pinia** - State management
- **Vue Router** - Official router
- **Axios** - HTTP client
- **Chart.js** - Charts and analytics
- **Lucide Icons** - Beautiful icons

---

## 🔐 **Default Admin Credentials**

Create admin user in Laravel:

```bash
cd backend
php artisan tinker

>>> App\Models\User::create([
    'email' => 'admin@parfumes.com',
    'password' => bcrypt('Admin@123'),
    'full_name' => 'Super Admin',
    'phone_number' => '12345678',
    'is_admin' => true
]);
```

Then login with:
- **Email:** admin@parfumes.com
- **Password:** Admin@123

---

## 🌐 **Environment Variables**

Create `.env` file:

```env
VITE_API_URL=http://localhost:8000/api
```

---

## 🚀 **Development**

```bash
# Start Laravel backend
cd backend
php artisan serve

# Start admin panel (in another terminal)
cd admin-panel
npm run dev
```

Access admin panel at: `http://localhost:3000`

---

## 📊 **Features Overview**

### **Dashboard Page**
- 📈 Total users count
- 🏠 Total properties count
- ⏳ Pending approvals
- ✅ Approved properties
- ❌ Rejected properties
- 📊 Pie chart (properties by category)
- 📊 Bar chart (properties by status)
- 👥 Recent users list
- 🏢 Recent properties list

### **Users Page**
- 📋 Paginated users table
- 🔍 Search functionality
- 🔄 Filter by status
- 👁️ View details
- 🔒 Activate/deactivate
- 📊 Properties count per user

### **Properties Page**
- 📋 Paginated properties table
- 🔍 Search functionality
- 🔄 Filter by status & category
- 👁️ View details with images
- ✅ Approve button
- ❌ Reject button
- 🗑️ Delete button
- 📧 Owner contact info

---

## 🎯 **Usage**

### **1. Login**
- Navigate to `http://localhost:3000`
- Enter admin credentials
- Click "تسجيل الدخول"

### **2. Dashboard**
- View statistics
- Check recent activities
- Analyze charts

### **3. Manage Users**
- Click "المستخدمون" in sidebar
- Search or filter users
- Toggle user status

### **4. Manage Properties**
- Click "العقارات" in sidebar
- Filter by status
- Approve/reject/delete properties

---

## 🔒 **Security**

- ✅ Token-based authentication
- ✅ Protected routes
- ✅ Auto-logout on 401
- ✅ CORS configured
- ✅ XSS protection
- ✅ Admin-only access

---

## 📱 **Responsive Design**

- ✅ Desktop optimized
- ✅ Tablet friendly
- ✅ Mobile responsive
- ✅ RTL support (Arabic)

---

## 🎨 **Customization**

### **Colors**
Edit `tailwind.config.js`:
```js
colors: {
  brown: {
    DEFAULT: '#8B4513',
    light: '#D2691E',
    dark: '#654321',
  }
}
```

### **API URL**
Edit `.env`:
```env
VITE_API_URL=https://your-domain.com/api
```

---

## 🐛 **Troubleshooting**

### **Issue: CORS Error**
```php
// backend/config/cors.php
'allowed_origins' => ['http://localhost:3000'],
```

### **Issue: 401 Unauthorized**
- Check if user is admin
- Verify token is saved
- Check backend is running

### **Issue: API not found**
- Verify backend URL in `.env`
- Check Laravel server is running
- Verify routes in `backend/routes/admin.php`

---

## 📚 **Documentation**

- [Vue.js Docs](https://vuejs.org/)
- [Tailwind CSS Docs](https://tailwindcss.com/)
- [Vite Docs](https://vitejs.dev/)
- [Pinia Docs](https://pinia.vuejs.org/)

---

## ✅ **Checklist**

- [x] Vue.js 3 setup
- [x] Tailwind CSS configured
- [x] Authentication system
- [x] Dashboard with statistics
- [x] Users management
- [x] Properties management
- [x] Charts and analytics
- [x] Responsive design
- [x] RTL support
- [x] Production ready

---

**🎉 Your Super Admin Panel is Ready!**

Built with ❤️ by Senior Vue.js + Laravel Engineer
