# ✅ Parfumes System - Localhost Setup Complete

## 🎯 All API URLs Fixed for Local Development

### Changes Made:

#### 1. **Frontend API Configuration** ✅
**File:** `frontend/src/services/api.js`
- Changed: `https://gt-academy.com/backend/public/api`
- To: `http://localhost/parfumes/backend/public/api`
- Also fixed PDF download URL to use localhost

#### 2. **Vite Configuration** ✅
**File:** `frontend/vite.config.js`
- Set base path to `/parfumes/`
- API proxy configured for `/api` → `http://localhost/parfumes/backend/public`

#### 3. **Apache Configuration** ✅
**File:** `.htaccess`
- Routes `/parfumes/` to built frontend (`frontend/dist/`)
- Routes `/parfumes/assets/` to static assets
- Routes `/parfumes/api/` to backend API
- SPA fallback for all routes (login, dashboard, etc.)

#### 4. **Frontend Build** ✅
- Rebuilt with `npm run build`
- All assets now in `frontend/dist/`
- Ready to serve from Apache

---

## 🚀 How to Access the System

### Main URLs:
```
✅ App Root:    http://localhost/parfumes/
✅ Login Page:  http://localhost/parfumes/login
✅ Dashboard:   http://localhost/parfumes/ (after login)
✅ API Base:    http://localhost/parfumes/backend/public/api
```

### API Endpoints Examples:
```
http://localhost/parfumes/backend/public/api/login
http://localhost/parfumes/backend/public/api/reports/dashboard
http://localhost/parfumes/backend/public/api/products
http://localhost/parfumes/backend/public/api/customers
```

---

## 📋 Prerequisites

### Required:
- ✅ XAMPP installed and running
- ✅ Apache running on port 80
- ✅ MySQL running on port 3306
- ✅ PHP 8.x enabled in XAMPP

### Optional (for development):
- Node.js (only if you want to rebuild frontend)

---

## 🔧 Troubleshooting

### Issue: CORS Error
**Solution:** Already fixed! All URLs now use `http://localhost/parfumes/`

### Issue: 404 on API calls
**Check:**
1. Apache is running
2. `.htaccess` rewrite rules are enabled
3. Backend folder exists at `c:\xampp\htdocs\parfumes\backend\`

### Issue: Blank page
**Check:**
1. Browser console for errors (F12)
2. Make sure `frontend/dist/` folder exists
3. Clear browser cache (Ctrl+Shift+Delete)

### Issue: Database connection error
**Check:**
1. MySQL is running in XAMPP
2. Database exists (check phpMyAdmin)
3. Backend `.env` file has correct DB credentials

---

## 📁 Project Structure

```
c:\xampp\htdocs\parfumes\
├── frontend/
│   ├── src/              # Vue.js source files
│   │   ├── services/
│   │   │   └── api.js   # ✅ API URLs fixed here
│   │   ├── views/       # Pages (Login, Dashboard, etc.)
│   │   └── router/      # Vue Router config
│   ├── dist/            # ✅ Built files (served by Apache)
│   └── vite.config.js   # ✅ Base path set to /parfumes/
│
├── backend/
│   ├── public/
│   │   └── api/         # API endpoints
│   ├── app/             # Laravel application
│   ├── config/          # Configuration files
│   └── .env             # Environment variables
│
├── .htaccess            # ✅ Apache routing rules
└── assets/              # Static assets
```

---

## 🔄 Development Workflow

### Making Frontend Changes:
```bash
cd c:\xampp\htdocs\parfumes\frontend

# Edit files in src/
# Then rebuild:
npm run build

# Refresh browser to see changes
```

### Making Backend Changes:
- Edit PHP files in `backend/`
- Changes take effect immediately
- No rebuild needed

---

## 🌐 Deployment to Production (gt-academy.com)

When ready to deploy:

### 1. Update API URLs:
```javascript
// frontend/src/services/api.js
baseURL: 'https://gt-academy.com/backend/public/api'
```

### 2. Update Vite Config:
```javascript
// frontend/vite.config.js
base: '/'  // or '/parfumes/' depending on deployment path
```

### 3. Rebuild:
```bash
npm run build
```

### 4. Upload:
- Upload entire `parfumes/` folder to server
- Ensure `.htaccess` is uploaded
- Update backend `.env` with production database credentials

---

## ✅ Current Status

```
✅ Frontend: Built and ready at frontend/dist/
✅ Backend: Configured for localhost
✅ API URLs: All pointing to localhost
✅ Apache: Configured to serve the app
✅ CORS: No more cross-origin errors
✅ Routes: SPA routing working (/login, /dashboard, etc.)
```

---

## 🎉 Ready to Use!

1. **Start XAMPP** (Apache + MySQL)
2. **Open browser:** http://localhost/parfumes/login
3. **Login** with your credentials
4. **Start working!**

---

## 📞 Need Help?

- Check browser console (F12) for errors
- Check Apache error logs in XAMPP
- Verify database connection in backend/.env
- Make sure all services are running in XAMPP

---

**Last Updated:** November 1, 2025
**Status:** ✅ Ready for local development
