@echo off
color 0A
echo ========================================
echo   COMPLETE XAMPP + DATABASE SETUP
echo ========================================
echo.
echo This script will:
echo [1] Fix MySQL startup issues
echo [2] Start Apache and MySQL
echo [3] Create perfume_store database
echo [4] Run all Laravel migrations
echo [5] Verify everything works
echo.
echo Make sure XAMPP Control Panel is closed!
echo.
pause

echo.
echo ========================================
echo STEP 1: MySQL Recovery
echo ========================================
echo.
taskkill /F /IM mysqld.exe 2>nul
taskkill /F /IM httpd.exe 2>nul
timeout /t 2 /nobreak >nul

if not exist "C:\xampp\mysql\backup" mkdir "C:\xampp\mysql\backup"
xcopy "C:\xampp\mysql\data\ib_logfile*" "C:\xampp\mysql\backup\" /Y 2>nul
del /F /Q "C:\xampp\mysql\data\ib_logfile0" 2>nul
del /F /Q "C:\xampp\mysql\data\ib_logfile1" 2>nul
del /F /Q "C:\xampp\mysql\data\*.pid" 2>nul

echo MySQL recovery complete.
echo.

echo ========================================
echo STEP 2: Starting Services
echo ========================================
echo.
echo Starting Apache...
start /B C:\xampp\apache\bin\httpd.exe
timeout /t 3 /nobreak >nul

echo Starting MySQL...
start /B C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone
timeout /t 5 /nobreak >nul

echo Checking services...
netstat -ano | findstr :80 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo [OK] Apache is running on port 80
) else (
    echo [ERROR] Apache failed to start!
    echo Please start Apache manually from XAMPP Control Panel
)

netstat -ano | findstr :3306 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo [OK] MySQL is running on port 3306
) else (
    echo [ERROR] MySQL failed to start!
    echo Please start MySQL manually from XAMPP Control Panel
    pause
    exit /b 1
)
echo.

echo ========================================
echo STEP 3: Creating Database
echo ========================================
echo.
timeout /t 3 /nobreak >nul
php create_db.php
if %errorlevel% neq 0 (
    echo [ERROR] Failed to create database!
    echo Make sure MySQL is running.
    pause
    exit /b 1
)
echo.

echo ========================================
echo STEP 4: Running Migrations
echo ========================================
echo.
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Migrations failed!
    echo Check your .env file settings.
    pause
    exit /b 1
)
echo.

echo ========================================
echo STEP 5: Verification
echo ========================================
echo.
php artisan db:show
echo.

echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo [SUCCESS] Everything is ready!
echo.
echo Database: perfume_store
echo Host: 127.0.0.1:3306
echo User: root
echo Password: (empty)
echo.
echo phpMyAdmin: http://127.0.0.1/phpmyadmin/
echo Your App: http://localhost:8080 (if frontend running)
echo.
echo All database tables have been created.
echo Your app can now save and retrieve data!
echo.
pause
