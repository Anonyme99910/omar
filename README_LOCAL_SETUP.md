# Parfumes System - Local Development Setup

## ✅ System Overview

This is the **Parfumes Management System** (نظام إدارة متجر العطور) - an Arabic RTL ERP system for perfume store management.

- **Live Production:** https://gt-academy.com/parfumes/
- **Local Development:** http://localhost:5173

---

## 🚀 Quick Start

### 1. Prerequisites

- ✅ XAMPP installed and running (Apache + MySQL)
- ✅ Node.js installed (v16 or higher)
- ✅ Git (optional, for version control)

### 2. Start the System

**Option A: Double-click to start**
```
START_LOCAL_DEV.bat
```

**Option B: Manual start**
```bash
cd c:\xampp\htdocs\parfumes\frontend
npm install
npm run dev
```

### 3. Access the Application

- **Frontend (Vue.js):** http://localhost:5173
- **Backend API:** http://localhost/parfumes/backend/public
- **Database:** phpMyAdmin at http://localhost/phpmyadmin

---

## 📁 Project Structure

```
c:\xampp\htdocs\parfumes\
├── frontend/              # Vue.js 3 frontend
│   ├── src/
│   │   ├── views/        # Login.vue, Dashboard.vue, etc.
│   │   ├── components/   # Reusable components
│   │   ├── router/       # Vue Router config
│   │   ├── stores/       # Pinia state management
│   │   └── services/     # API services
│   ├── dist/             # Built files (after npm run build)
│   └── package.json
│
├── backend/              # Laravel/PHP backend
│   ├── public/
│   │   └── index.php    # API entry point
│   └── vendor/          # PHP dependencies
│
├── assets/              # Static assets
├── .htaccess           # Apache rewrite rules
└── index.php           # Root entry point
```

---

## 🔧 Development Workflow

### Frontend Development
```bash
cd frontend
npm run dev          # Start dev server
npm run build        # Build for production
npm run preview      # Preview production build
```

### Backend API
- API endpoints are at: `http://localhost/parfumes/backend/public/api/`
- Vite proxy automatically forwards `/api/*` requests to backend

---

## 🌐 Deployment to gt-academy.com

### Build for Production
```bash
cd frontend
npm run build
```

### Upload to Server
1. Build creates files in `frontend/dist/`
2. Upload entire `parfumes/` folder to server
3. Ensure `.htaccess` is configured correctly
4. Database credentials in backend config

---

## 🐛 Troubleshooting

### Issue: Directory listing instead of login page
**Solution:** Run `START_LOCAL_DEV.bat` to start Vite dev server

### Issue: API 404 errors
**Solution:** Check XAMPP Apache is running and backend path is correct

### Issue: Blank page
**Solution:** Check browser console for errors, ensure npm dependencies are installed

### Issue: Database connection error
**Solution:** Update database credentials in backend config files

---

## 📝 Default Login Credentials

Check with system administrator for login credentials.

---

## 🛠 Tech Stack

- **Frontend:** Vue.js 3, Vite, TailwindCSS, Pinia, Vue Router
- **Backend:** PHP, Laravel components
- **Database:** MySQL
- **Server:** Apache (XAMPP)
- **UI:** RTL Arabic interface with Cairo font

---

## 📞 Support

For issues or questions, contact the development team.

---

**Last Updated:** November 1, 2025
