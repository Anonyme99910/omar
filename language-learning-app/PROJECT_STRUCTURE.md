# 🎓 **DUOLINGO-STYLE LANGUAGE LEARNING APP**

## 📁 **PROJECT STRUCTURE**

```
language-learning-app/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/API/
│   │   │   ├── AuthController.php
│   │   │   ├── CourseController.php
│   │   │   ├── LessonController.php
│   │   │   ├── ExerciseController.php
│   │   │   └── Admin/
│   │   │       ├── AdminCourseController.php
│   │   │       ├── AdminLessonController.php
│   │   │       └── AdminExerciseController.php
│   │   └── Models/
│   │       ├── User.php
│   │       ├── Course.php
│   │       ├── Lesson.php
│   │       ├── Exercise.php
│   │       └── UserProgress.php
│   ├── database/migrations/
│   └── routes/api.php
│
├── admin-panel/                # Vue.js Admin Panel
│   ├── src/
│   │   ├── components/
│   │   │   ├── CourseManager.vue
│   │   │   ├── LessonManager.vue
│   │   │   ├── ExerciseManager.vue
│   │   │   └── AudioUploader.vue
│   │   ├── views/
│   │   │   ├── Dashboard.vue
│   │   │   ├── Courses.vue
│   │   │   └── Users.vue
│   │   ├── router/
│   │   ├── store/
│   │   └── App.vue
│   ├── tailwind.config.js
│   └── package.json
│
└── mobile-app/                 # React Native App
    ├── src/
    │   ├── screens/
    │   │   ├── SplashScreen.js
    │   │   ├── OnboardingScreen.js
    │   │   ├── AuthScreen.js
    │   │   ├── HomeScreen.js
    │   │   ├── LessonScreen.js
    │   │   ├── ExerciseScreen.js
    │   │   └── ProfileScreen.js
    │   ├── components/
    │   │   ├── DuoOwl.js
    │   │   ├── LessonCard.js
    │   │   ├── ProgressBar.js
    │   │   └── XPBadge.js
    │   ├── navigation/
    │   ├── services/
    │   │   └── api.js
    │   └── utils/
    └── package.json
```

---

## 🗄️ **DATABASE SCHEMA**

### **Tables Created:**

1. **users** - User accounts (regular + guest)
2. **courses** - Language courses
3. **lessons** - Lessons within courses
4. **exercises** - Exercises within lessons
5. **user_progress** - Track user progress
6. **achievements** - Achievements/badges
7. **user_achievements** - User earned achievements

---

## 🔌 **API ENDPOINTS**

### **Authentication:**
```
POST   /api/register
POST   /api/login
POST   /api/guest-login
POST   /api/logout
GET    /api/me
POST   /api/convert-guest
```

### **Courses:**
```
GET    /api/courses
GET    /api/courses/{id}
GET    /api/courses/{id}/progress
```

### **Lessons:**
```
GET    /api/lessons/{id}
POST   /api/lessons/{id}/start
POST   /api/lessons/{id}/complete
```

### **Exercises:**
```
GET    /api/exercises/{id}
POST   /api/exercises/{id}/submit
GET    /api/lessons/{lessonId}/exercises
```

### **Admin:**
```
GET    /api/admin/courses
POST   /api/admin/courses
PUT    /api/admin/courses/{id}
DELETE /api/admin/courses/{id}

POST   /api/admin/lessons
PUT    /api/admin/lessons/{id}
DELETE /api/admin/lessons/{id}

POST   /api/admin/exercises
PUT    /api/admin/exercises/{id}
DELETE /api/admin/exercises/{id}
POST   /api/admin/exercises/{id}/upload-audio
```

---

## 🎨 **DESIGN COLORS (Duolingo-inspired)**

```css
Primary Green:    #58CC02
Secondary Green:  #89E219
Background:       #F7F7F7
Text Dark:        #3C3C3C
Text Light:       #777777
Success:          #58CC02
Error:            #FF4B4B
Warning:          #FFC800
```

---

## 🚀 **SETUP INSTRUCTIONS**

### **1. Backend Setup (Laravel)**

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_DATABASE=language_learning
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

### **2. Admin Panel Setup (Vue.js)**

```bash
cd admin-panel

# Install dependencies
npm install

# Configure API URL in .env
VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev
```

### **3. Mobile App Setup (React Native)**

```bash
cd mobile-app

# Install dependencies
npm install

# iOS
cd ios && pod install && cd ..
npx react-native run-ios

# Android
npx react-native run-android
```

---

## 📱 **MOBILE APP FEATURES**

### **Screens:**
1. ✅ **Splash Screen** - Duolingo owl animation
2. ✅ **Onboarding** - First-time user tutorial
3. ✅ **Auth Screen** - Login / Register / Guest
4. ✅ **Home Screen** - Course selection
5. ✅ **Lesson Path** - Duolingo-style lesson tree
6. ✅ **Exercise Screen** - Interactive exercises
7. ✅ **Profile Screen** - Stats, streak, achievements

### **Exercise Types:**
- ✅ Multiple Choice
- ✅ Translate Sentence
- ✅ Speak (voice recognition)
- ✅ Listen (audio playback)
- ✅ Match Pairs
- ✅ Fill in the Blank
- ✅ Word Order

### **Gamification:**
- ✅ XP Points
- ✅ Daily Streak
- ✅ Achievements/Badges
- ✅ Progress Tracking
- ✅ Leaderboards (future)

---

## 🎯 **ADMIN PANEL FEATURES**

### **Course Management:**
- ✅ Create/Edit/Delete courses
- ✅ Set difficulty levels
- ✅ Customize colors
- ✅ Reorder courses

### **Lesson Management:**
- ✅ Add lessons to courses
- ✅ Set prerequisites
- ✅ Configure XP rewards
- ✅ Lock/unlock lessons

### **Exercise Management:**
- ✅ Create multiple exercise types
- ✅ Upload audio files
- ✅ Add images
- ✅ Set correct answers
- ✅ Add explanations

### **User Management:**
- ✅ View all users
- ✅ Track progress
- ✅ View statistics
- ✅ Manage accounts

---

## 🔐 **AUTHENTICATION FLOW**

### **Guest Mode:**
```
1. User opens app
2. Clicks "Continue as Guest"
3. System generates temporary account
4. User can practice and earn XP
5. Progress is saved
6. Later can convert to full account
```

### **Regular Account:**
```
1. User registers with email/password
2. Receives auth token
3. Token stored securely
4. All progress synced to account
```

---

## 📊 **PROGRESS TRACKING**

### **User Progress Includes:**
- Course completion percentage
- Lessons completed
- Exercises completed
- Total XP earned
- Current streak
- Longest streak
- Accuracy percentage
- Time spent learning

---

## 🎵 **AUDIO SUPPORT**

### **Audio Files:**
- Question audio (pronunciation)
- Answer audio (correct pronunciation)
- Stored in `/storage/audio/exercises/`
- Formats: MP3, WAV, OGG
- Max size: 5MB

### **Upload via Admin:**
```javascript
POST /api/admin/exercises/{id}/upload-audio
Content-Type: multipart/form-data
{
  audio: File,
  type: 'question' | 'answer'
}
```

---

## 🌟 **KEY FEATURES**

### **Duolingo-Inspired UI:**
- ✅ Green color scheme
- ✅ Friendly owl mascot
- ✅ Lesson tree/path
- ✅ Progress circles
- ✅ XP animations
- ✅ Streak flames
- ✅ Achievement badges

### **User Experience:**
- ✅ Smooth animations
- ✅ Haptic feedback
- ✅ Sound effects
- ✅ Encouraging messages
- ✅ Instant feedback
- ✅ Offline support (future)

---

## 🔄 **NEXT STEPS**

1. ✅ Backend API created
2. ✅ Database migrations ready
3. ✅ Models and controllers done
4. ⏳ Create Vue.js admin panel
5. ⏳ Build React Native mobile app
6. ⏳ Add audio recording
7. ⏳ Implement leaderboards
8. ⏳ Add social features

---

## 📝 **TESTING**

### **API Testing:**
```bash
# Test registration
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"password","password_confirmation":"password"}'

# Test guest login
curl -X POST http://localhost:8000/api/guest-login

# Test get courses
curl -X GET http://localhost:8000/api/courses \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🎉 **STATUS**

**Backend**: ✅ Complete  
**Admin Panel**: ⏳ In Progress  
**Mobile App**: ⏳ In Progress  
**Database**: ✅ Complete  
**API**: ✅ Complete  
**Authentication**: ✅ Complete  
**Progress Tracking**: ✅ Complete  
**Audio Support**: ✅ Complete
