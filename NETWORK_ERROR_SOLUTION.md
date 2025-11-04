# 🔧 **NETWORK REQUEST FAILED - COMPLETE FIX**

## 🔥 **THE PROBLEM**

You're getting:
```
API request failed: Network request failed
API: Registration failed error
```

Even though `test-connection.php` works!

**This means:** The app is using the WRONG API URL (probably localhost instead of your computer's IP)

---

## ✅ **THE COMPLETE FIX**

### **Step 1: Stop Expo Server**
In the terminal, press:
```
Ctrl + C
```

### **Step 2: Run Fix Script**
```bash
.\fix-and-restart.bat
```

This will:
1. ✅ Verify .env file
2. ✅ Kill old processes
3. ✅ Clear Metro cache
4. ✅ Start fresh Expo server

### **Step 3: Check Expo Logs**
When app starts, look for this line:
```
🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api
```

**✅ If you see your IP (10.50.240.89):** Good! .env is loaded  
**❌ If you see localhost:** .env is NOT loaded!

---

## 🚨 **IF .ENV IS NOT LOADED**

### **Manual Fix:**

1. **Stop Expo** (`Ctrl + C`)

2. **Delete .env and recreate:**
   ```bash
   del .env
   echo EXPO_PUBLIC_API_URL=http://10.50.240.89/parfumes/backend/public/api > .env
   ```

3. **Clear everything:**
   ```bash
   rmdir /s /q node_modules\.cache
   ```

4. **Start with clear flag:**
   ```bash
   npx expo start --clear
   ```

5. **Check logs again for:**
   ```
   🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api
   ```

---

## 🧪 **VERIFICATION STEPS**

### **1. Check .env File:**
```bash
type .env
```
Should show:
```
EXPO_PUBLIC_API_URL=http://10.50.240.89/parfumes/backend/public/api
```

### **2. Check Expo Logs:**
Look for:
```
🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api
```

### **3. Try Registration:**
When you try to register, you should see:
```
API Request: POST http://10.50.240.89/parfumes/backend/public/api/register
```

**NOT:**
```
API Request: POST http://localhost:8000/api/register  ← WRONG!
```

---

## 📊 **WHAT TO LOOK FOR IN LOGS**

### **✅ CORRECT (Will Work):**
```
🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api
API Request: POST http://10.50.240.89/parfumes/backend/public/api/register
API: Registering user...
API: Registration successful
```

### **❌ WRONG (Will Fail):**
```
🔧 API_URL configured: http://localhost:8000/api
API Request: POST http://localhost:8000/api/register
API Request Failed: Network request failed
```

---

## 🎯 **QUICK FIX COMMANDS**

```bash
# Stop server
Ctrl + C

# Clear cache and restart
npx expo start --clear

# Or use fix script
.\fix-and-restart.bat
```

---

## 💡 **WHY THIS HAPPENS**

1. **Expo caches the .env file**
2. **Changes to .env don't reload automatically**
3. **Need to restart with --clear flag**
4. **Metro bundler caches old values**

---

## 🔥 **FINAL CHECKLIST**

- [ ] Stop Expo server
- [ ] Verify .env has correct IP
- [ ] Clear Metro cache
- [ ] Start with `npx expo start --clear`
- [ ] Check logs for correct API_URL
- [ ] Scan QR code
- [ ] Try registration
- [ ] Check logs show correct URL

---

## 🎉 **EXPECTED RESULT**

After fixing, when you try to register:

**Expo Logs:**
```
🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api
API Request: POST http://10.50.240.89/parfumes/backend/public/api/register
API: Registering user... {email: "test@example.com", ...}
API: Registration successful {user: {...}, token: "..."}
```

**App:**
- ✅ Registration succeeds
- ✅ User created
- ✅ Redirects to home
- ✅ Shows properties

---

**🔥 Run the fix script now:**
```bash
.\fix-and-restart.bat
```

**Then check the logs for the API_URL!**
