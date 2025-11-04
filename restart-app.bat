@echo off
cls
echo ========================================
echo   RESTARTING MOBILE APP
echo   With Fixed Network Configuration
echo ========================================
echo.

echo ✅ .env file updated with computer IP
echo.
type .env
echo.

echo ========================================
echo   STARTING EXPO SERVER
echo ========================================
echo.
echo Your Computer IP: 10.50.240.89
echo Backend API: http://10.50.240.89/parfumes/backend/public/api
echo.
echo IMPORTANT:
echo   1. Make sure phone and computer are on SAME WiFi
echo   2. Scan the QR code with Expo Go
echo   3. Wait 1-2 minutes for first load
echo.
echo Test Credentials:
echo   Email: ahmed@example.com
echo   Password: password123
echo.
echo ========================================
echo.

npm run dev
