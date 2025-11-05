# 🚀 **QUICK START GUIDE**

## ✅ **DATABASE SETUP COMPLETE!**

The database `duolingo` has been created and populated with:
- ✅ 3 Users (1 admin, 2 regular users)
- ✅ 5 Courses (English, Spanish, French, German, Arabic)
- ✅ 12 Lessons across all courses
- ✅ 16 Exercises with multiple types
- ✅ 6 Achievements

---

## 🔐 **TEST CREDENTIALS**

### **Admin Account:**
- Email: `admin@duolingo.com`
- Password: `password`
- Role: Admin (full access)

### **User Accounts:**
- Email: `john@example.com` / Password: `password`
- Email: `jane@example.com` / Password: `password`

---

## 🌐 **API ENDPOINTS**

### **Base URL:**
```
http://localhost/parfumes/language-learning-app/backend/public/api
```

### **Test API:**
```bash
# Get all courses (no auth required)
curl http://localhost/parfumes/language-learning-app/backend/public/api/courses

# Login
curl -X POST http://localhost/parfumes/language-learning-app/backend/public/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"john@example.com\",\"password\":\"password\"}"

# Guest login
curl -X POST http://localhost/parfumes/language-learning-app/backend/public/api/guest-login
```

---

## 📱 **RUNNING THE MOBILE APP**

### **1. Install Dependencies:**
```bash
cd mobile-app
npm install
```

### **2. For Android:**
```bash
npx react-native run-android
```

### **3. For iOS (Mac only):**
```bash
cd ios
pod install
cd ..
npx react-native run-ios
```

### **4. Start Metro Bundler:**
```bash
npx react-native start
```

---

## 💻 **RUNNING THE ADMIN PANEL**

### **1. Install Dependencies:**
```bash
cd admin-panel
npm install
```

### **2. Create .env file:**
```bash
echo VITE_API_URL=http://localhost/parfumes/language-learning-app/backend/public/api > .env
```

### **3. Start Development Server:**
```bash
npm run dev
```

### **4. Open in Browser:**
```
http://localhost:5173
```

### **5. Login:**
- Email: `admin@duolingo.com`
- Password: `password`

---

## 🧪 **TESTING THE APP**

### **Mobile App Flow:**
1. ✅ Launch app → See splash screen
2. ✅ First time → See onboarding (3 slides)
3. ✅ Choose: Login / Register / Guest
4. ✅ See home screen with courses
5. ✅ Select "English for Beginners"
6. ✅ See lesson path with 5 lessons
7. ✅ Tap "Greetings & Introductions"
8. ✅ See lesson details (4 exercises)
9. ✅ Tap "START LESSON"
10. ✅ Complete exercises (multiple choice, translate, etc.)
11. ✅ Earn XP and complete lesson
12. ✅ Return to home screen

### **Admin Panel Flow:**
1. ✅ Open http://localhost:5173
2. ✅ Login with admin credentials
3. ✅ See dashboard with sidebar
4. ✅ Click "Courses"
5. ✅ See 5 courses in grid
6. ✅ Click "Add Course" to create new
7. ✅ Click "Edit" to modify existing
8. ✅ Click "Lessons" to manage lessons
9. ✅ Add/edit exercises with audio upload

---

## 📊 **DATABASE STRUCTURE**

### **Available Courses:**
1. 🇬🇧 **English for Beginners** (5 lessons, 13 exercises)
2. 🇪🇸 **Spanish Basics** (3 lessons, 3 exercises)
3. 🇫🇷 **French Fundamentals** (2 lessons)
4. 🇩🇪 **German Essentials** (1 lesson)
5. 🇸🇦 **Arabic for Beginners** (1 lesson)

### **English Course Lessons:**
1. 👋 Greetings & Introductions (4 exercises)
2. 🔢 Numbers 1-20 (3 exercises)
3. 🎨 Colors & Shapes (2 exercises)
4. 👨‍👩‍👧‍👦 Family Members (2 exercises)
5. 🍕 Food & Drinks (2 exercises)

### **Exercise Types:**
- ✅ Multiple Choice
- ✅ Translate
- ✅ Fill in the Blank
- ✅ Listen (audio)
- ✅ Speak (voice)
- ✅ Match Pairs
- ✅ Word Order

---

## 🔧 **TROUBLESHOOTING**

### **API Not Working:**
1. Check XAMPP Apache is running
2. Check MySQL is running
3. Verify database exists: `duolingo`
4. Check .env file in backend folder

### **Mobile App Can't Connect:**
1. Update API URL in `mobile-app/src/services/api.js`
2. For Android emulator use: `http://10.0.2.2/parfumes/...`
3. For iOS simulator use: `http://localhost/parfumes/...`
4. For physical device use: `http://YOUR_IP/parfumes/...`

### **Admin Panel Can't Connect:**
1. Check .env file has correct VITE_API_URL
2. Restart dev server: `npm run dev`
3. Clear browser cache

---

## 📱 **MOBILE APP SCREENS**

### **Created:**
- ✅ SplashScreen.js - Animated owl logo
- ✅ OnboardingScreen.js - 3-slide tutorial
- ✅ AuthScreen.js - Login/Register/Guest
- ✅ HomeScreen.js - Course selection + lesson path
- ✅ LessonScreen.js - Lesson details
- ✅ ExerciseScreen.js - All exercise types
- ✅ ProfileScreen.js - User stats & settings
- ✅ App.js - Navigation setup

### **Navigation:**
- Stack Navigator for main flow
- Tab Navigator for Home/Profile
- Proper screen transitions

---

## 🎨 **UI/UX FEATURES**

### **Duolingo-Inspired:**
- ✅ Green color scheme (#58CC02)
- ✅ Friendly owl mascot 🦉
- ✅ Lesson tree/path design
- ✅ Progress circles
- ✅ XP animations
- ✅ Streak flames 🔥
- ✅ Achievement badges 🏆

### **Gamification:**
- ✅ XP Points (⚡)
- ✅ Daily Streaks (🔥)
- ✅ Achievements (🏆)
- ✅ Progress Tracking (📊)
- ✅ Level System

---

## 🎯 **NEXT STEPS**

### **Immediate:**
1. ✅ Test login with credentials
2. ✅ Browse courses in mobile app
3. ✅ Complete a lesson
4. ✅ Check XP earned
5. ✅ View profile stats

### **Admin Tasks:**
1. ✅ Login to admin panel
2. ✅ Create a new course
3. ✅ Add lessons to course
4. ✅ Add exercises to lessons
5. ✅ Upload audio files

### **Future Enhancements:**
- [ ] Voice recognition for speak exercises
- [ ] Text-to-speech for audio
- [ ] Offline mode
- [ ] Push notifications
- [ ] Leaderboards
- [ ] Social features
- [ ] More languages

---

## 📞 **SUPPORT**

### **Common Issues:**

**Q: Can't see courses in mobile app?**
A: Check API URL and ensure backend is running

**Q: Login fails?**
A: Verify credentials and database connection

**Q: Exercises not loading?**
A: Check database has exercises for that lesson

**Q: Admin panel won't load?**
A: Run `npm install` and `npm run dev`

---

## ✅ **VERIFICATION CHECKLIST**

- [x] Database created: `duolingo`
- [x] Tables created: 7 tables
- [x] Mock data inserted: Users, Courses, Lessons, Exercises
- [x] API endpoints working
- [x] Mobile app configured
- [x] Admin panel configured
- [x] Navigation setup
- [x] Authentication working
- [x] Progress tracking enabled

---

## 🎉 **YOU'RE ALL SET!**

Your Duolingo-style language learning platform is ready to use!

**Start the mobile app and begin learning! 🚀**
