@echo off
echo ========================================
echo   Deploying Frontend to XAMPP
echo ========================================
echo.

cd frontend

echo [1/3] Building frontend...
call npm run build
if %errorlevel% neq 0 (
    echo ERROR: Build failed!
    pause
    exit /b 1
)

echo.
echo [2/3] Copying files to parfumes root...

REM Create assets directory if it doesn't exist
if not exist "..\assets" mkdir "..\assets"

REM Copy index.html to root
copy /Y "dist\index.html" "..\index.html"

REM Copy all assets
xcopy /E /Y /I "dist\assets\*" "..\assets\"

echo.
echo [3/3] Cleaning up...
echo.

echo ========================================
echo   Deployment Complete!
echo ========================================
echo.
echo Access your app at:
echo http://localhost/parfumes/
echo.
pause
