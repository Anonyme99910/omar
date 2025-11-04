@echo off
cls
echo ========================================
echo   FIXING AND RESTARTING APP
echo ========================================
echo.

echo [1/4] Checking .env file...
if exist ".env" (
    echo   Current .env:
    type .env
    echo.
) else (
    echo   Creating .env...
    echo EXPO_PUBLIC_API_URL=http://10.50.240.89/parfumes/backend/public/api > .env
)

echo.
echo [2/4] Killing old Expo processes...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :8081') do (
    taskkill /F /PID %%a 2>nul
)
echo   Done!

echo.
echo [3/4] Clearing Metro bundler cache...
if exist "node_modules\.cache" (
    rmdir /s /q "node_modules\.cache"
)
echo   Done!

echo.
echo [4/4] Starting Expo with fresh cache...
echo.
echo ========================================
echo   IMPORTANT INFORMATION
echo ========================================
echo.
echo Backend API: http://10.50.240.89/parfumes/backend/public/api
echo.
echo When app loads, check Expo logs for:
echo   "🔧 API_URL configured: http://10.50.240.89/parfumes/backend/public/api"
echo.
echo If you see "localhost" instead, the .env is not loaded!
echo.
echo Test Credentials:
echo   Email: test@example.com
echo   Password: password123
echo.
echo ========================================
echo.

npx expo start --clear
