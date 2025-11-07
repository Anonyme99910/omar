# ⚠️ IMPORTANT: How to Run Parfumes System Locally

## The Problem You're Seeing

You're getting this error:
```
Uncaught TypeError: Failed to resolve module specifier "vue"
```

This happens because **Vue.js source files cannot run directly in Apache/XAMPP**. They need to be either:
1. Served through Vite dev server, OR
2. Built into static files first

---

## ✅ Solution: Use Vite Dev Server

### Step 1: Install Node.js (if not installed)
Download from: https://nodejs.org/
- Choose LTS version
- Install with default settings

### Step 2: Open Command Prompt
```
Press Windows Key + R
Type: cmd
Press Enter
```

### Step 3: Navigate to Frontend Folder
```bash
cd c:\xampp\htdocs\parfumes\frontend
```

### Step 4: Install Dependencies (First Time Only)
```bash
npm install
```

### Step 5: Start Dev Server
```bash
npm run dev
```

### Step 6: Open Browser
The terminal will show:
```
  ➜  Local:   http://localhost:5173/
```

Open that URL in your browser!

---

## 🎯 Quick Start (After First Setup)

1. Open Command Prompt
2. Run these commands:
```bash
cd c:\xampp\htdocs\parfumes\frontend
npm run dev
```
3. Open http://localhost:5173

---

## 📝 Alternative: Build for XAMPP

If you want to use XAMPP directly without Vite:

### Step 1: Build the Project
```bash
cd c:\xampp\htdocs\parfumes\frontend
npm run build
```

### Step 2: Copy Built Files
The build creates files in `frontend/dist/`

### Step 3: Update Root index.php
Point it to the built files instead of source files.

---

## 🔧 Why This Happens

- **Vue.js uses ES modules** (`import vue from 'vue'`)
- **Browsers can't resolve** `node_modules` paths directly
- **Vite dev server** translates these imports on-the-fly
- **OR you need to build** the project into browser-ready files

---

## ✅ Recommended Workflow

**For Development:**
```bash
npm run dev
→ Use http://localhost:5173
```

**For Production (gt-academy.com):**
```bash
npm run build
→ Upload dist/ folder contents
```

---

## 🆘 Still Having Issues?

1. **Check Node.js is installed:**
   ```bash
   node --version
   ```

2. **Check npm is installed:**
   ```bash
   npm --version
   ```

3. **Clear npm cache:**
   ```bash
   npm cache clean --force
   npm install
   ```

4. **Check XAMPP MySQL is running** (for backend API)

---

**Bottom Line:** Don't access `localhost/parfumes/` directly. Use `npm run dev` and access `localhost:5173` instead!
