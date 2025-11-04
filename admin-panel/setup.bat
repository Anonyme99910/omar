@echo off
cls
echo ========================================
echo   PARFUMES ADMIN PANEL - SETUP
echo   Vue.js + Tailwind CSS
echo ========================================
echo.

echo [1/3] Installing dependencies...
call npm install

echo.
echo [2/3] Creating .env file...
if not exist ".env" (
    echo VITE_API_URL=http://localhost/parfumes/backend/public/api > .env
    echo   .env file created
) else (
    echo   .env already exists
)

echo.
echo [3/3] Ready to start!
echo.
echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo Backend API:
echo   http://localhost/parfumes/backend/public/api
echo.
echo To start the admin panel:
echo   npm run dev
echo.
echo Then open: http://localhost:3000
echo.
echo Login with:
echo   Email: admin@parfumes.com
echo   Password: Admin@123
echo.
pause
