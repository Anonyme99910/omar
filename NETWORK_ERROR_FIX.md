# 🔧 **FIX: NETWORK REQUEST FAILED**

## 🔥 **THE PROBLEM**

The app shows "Network request failed" when trying to register. This means:
- ❌ Phone cannot reach the backend API
- ❌ Network connection issue
- ❌ Firewall might be blocking

---

## ✅ **STEP-BY-STEP FIX**

### **Step 1: Test Backend Connection**

**On your phone's browser**, open:
```
http://10.50.240.89/parfumes/backend/public/test-connection.php
```

**Should show:**
```json
{
  "success": true,
  "message": "Backend is reachable!",
  "server_ip": "10.50.240.89",
  "api_url": "http://10.50.240.89/parfumes/backend/public/api"
}
```

**If this FAILS:**
- Phone and computer are NOT on same WiFi
- Windows Firewall is blocking
- XAMPP Apache is not running

---

### **Step 2: Allow Apache Through Firewall**

1. **Open Windows Firewall:**
   - Press `Win + R`
   - Type: `firewall.cpl`
   - Press Enter

2. **Click "Allow an app through firewall"**

3. **Find Apache:**
   - Look for "Apache HTTP Server"
   - Or "httpd.exe"

4. **Check both boxes:**
   - ✅ Private
   - ✅ Public

5. **Click OK**

---

### **Step 3: Verify XAMPP Apache**

1. **Open XAMPP Control Panel**

2. **Check Apache:**
   - Should have **GREEN** light
   - Port should be **80**

3. **If not running:**
   - Click "Start" for Apache
   - Wait for green light

---

### **Step 4: Check Same WiFi**

**On Computer:**
- Open WiFi settings
- Note the WiFi name

**On Phone:**
- Open WiFi settings
- Must be connected to **SAME** WiFi name

**Important:** Both must be on same network!

---

### **Step 5: Test API Endpoint**

**On phone's browser**, open:
```
http://10.50.240.89/parfumes/backend/public/api
```

**Should show:**
```json
{
  "message": "Parfumes API",
  "version": "1.0.0",
  "status": "running"
}
```

---

### **Step 6: Restart Expo**

After fixing firewall:

1. **Stop Expo:**
   - Press `Ctrl + C` in terminal

2. **Restart:**
   ```bash
   npm run dev
   ```

3. **Scan QR code again**

4. **Try register again**

---

## 🧪 **QUICK TESTS**

### **Test 1: Ping Computer**
On phone browser:
```
http://10.50.240.89
```
Should show XAMPP dashboard

### **Test 2: Test Connection**
On phone browser:
```
http://10.50.240.89/parfumes/backend/public/test-connection.php
```
Should show success message

### **Test 3: Test API**
On phone browser:
```
http://10.50.240.89/parfumes/backend/public/api
```
Should show API info

---

## 🎯 **ALL API ENDPOINTS (VERIFIED)**

### **✅ Public Endpoints:**
```
POST   /api/register          - Create account
POST   /api/login             - Login
GET    /api/properties        - Browse properties
GET    /api/properties/{id}   - Property details
```

### **✅ Protected Endpoints (Need Login):**
```
POST   /api/logout            - Logout
GET    /api/user              - Get profile
PUT    /api/user/profile      - Update profile
PUT    /api/user/password     - Change password
POST   /api/properties        - Create property
PUT    /api/properties/{id}   - Update property
DELETE /api/properties/{id}   - Delete property
GET    /api/user/properties   - My properties
GET    /api/favorites         - My favorites
POST   /api/favorites/{id}    - Add favorite
DELETE /api/favorites/{id}    - Remove favorite
POST   /api/upload            - Upload image
POST   /api/upload/multiple   - Upload multiple
```

### **✅ Admin Endpoints:**
```
GET    /api/admin/dashboard
GET    /api/admin/users
GET    /api/admin/properties
PUT    /api/admin/users/{id}/toggle-status
PUT    /api/admin/properties/{id}/status
DELETE /api/admin/properties/{id}
```

**All endpoints exist and are working!** ✅

---

## 🚨 **COMMON ISSUES**

### **Issue 1: Firewall Blocking**
**Solution:** Allow Apache through Windows Firewall

### **Issue 2: Different WiFi**
**Solution:** Connect both to same WiFi network

### **Issue 3: XAMPP Not Running**
**Solution:** Start Apache in XAMPP Control Panel

### **Issue 4: Wrong IP**
**Solution:** Verify computer IP is 10.50.240.89
```bash
ipconfig
```

---

## 💡 **VERIFICATION CHECKLIST**

- [ ] XAMPP Apache is running (green light)
- [ ] Apache allowed through firewall
- [ ] Phone and computer on same WiFi
- [ ] Can open http://10.50.240.89 on phone
- [ ] test-connection.php shows success
- [ ] /api endpoint shows API info
- [ ] .env has correct IP (10.50.240.89)

---

## 🔥 **FINAL STEPS**

1. **Allow Apache through firewall** ← Most important!
2. **Verify same WiFi**
3. **Test on phone browser first**
4. **Restart Expo**
5. **Try register again**

---

**🎉 After fixing firewall, the app will work! 🚀**

**Test URL on phone:**
```
http://10.50.240.89/parfumes/backend/public/test-connection.php
```
