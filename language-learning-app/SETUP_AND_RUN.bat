@echo off
echo ========================================
echo DUOLINGO-STYLE LANGUAGE LEARNING APP
echo Complete Setup and Run Script
echo ========================================
echo.

echo [1/5] Setting up database...
echo Importing SQL file into MySQL...
mysql -u root -p -e "source C:\xampp\htdocs\parfumes\language-learning-app\backend\database\duolingo_complete.sql"
if %errorlevel% neq 0 (
    echo ERROR: Database import failed!
    echo Please run this command manually:
    echo mysql -u root -p ^< C:\xampp\htdocs\parfumes\language-learning-app\backend\database\duolingo_complete.sql
    pause
    exit /b 1
)
echo Database setup complete!
echo.

echo [2/5] Setting up Laravel backend...
cd backend
if not exist vendor (
    echo Installing Composer dependencies...
    call composer install
)

if not exist .env (
    echo Copying .env file...
    copy .env.example .env
)

echo Generating application key...
php artisan key:generate

echo Creating storage link...
php artisan storage:link

echo.
echo [3/5] Backend is ready!
echo.

echo [4/5] Starting services...
echo.
echo Starting Laravel development server...
start "Laravel Server" cmd /k "cd C:\xampp\htdocs\parfumes\language-learning-app\backend && php artisan serve --host=localhost --port=8000"
echo Laravel server started at http://localhost:8000
echo.

timeout /t 3

echo [5/5] Setup complete!
echo.
echo ========================================
echo SERVICES RUNNING:
echo ========================================
echo Backend API: http://localhost/parfumes/language-learning-app/backend/public/api
echo Laravel Dev: http://localhost:8000/api
echo.
echo ========================================
echo NEXT STEPS:
echo ========================================
echo.
echo For Admin Panel:
echo   cd admin-panel
echo   npm install
echo   npm run dev
echo.
echo For Mobile App:
echo   cd mobile-app
echo   npm install
echo   npx react-native run-android
echo.
echo ========================================
echo TEST CREDENTIALS:
echo ========================================
echo Admin: admin@duolingo.com / password
echo User:  john@example.com / password
echo.
echo Press any key to open browser...
pause
start http://localhost:8000/api/courses
