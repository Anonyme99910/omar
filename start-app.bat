@echo off
cls
echo ========================================
echo   STARTING PARFUMES MOBILE APP
echo   React Native + Expo
echo ========================================
echo.

echo [1/3] Checking .env file...
if exist ".env" (
    echo   ✅ .env file exists
    type .env
) else (
    echo   ⚠️  Creating .env file...
    echo EXPO_PUBLIC_API_URL=http://localhost/parfumes/backend/public/api > .env
    echo   ✅ .env file created
)

echo.
echo [2/3] Installing dependencies...
call npm install

echo.
echo [3/3] Starting Expo development server...
echo.
echo ========================================
echo   MOBILE APP STARTING
echo ========================================
echo.
echo Backend API: http://localhost/parfumes/backend/public/api
echo.
echo Instructions:
echo   1. Wait for QR code to appear
echo   2. Install "Expo Go" app on your phone
echo   3. Scan the QR code with Expo Go
echo   4. App will load on your phone
echo.
echo Test Credentials:
echo   Email: ahmed@example.com
echo   Password: password123
echo.
echo ========================================
echo.

npm run dev
