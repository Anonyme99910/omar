@echo off
echo ========================================
echo Creating Database and Running Migrations
echo ========================================
echo.

echo Step 1: Creating database...
php create_db.php
if %errorlevel% neq 0 (
    echo Failed to create database!
    pause
    exit /b 1
)
echo.

echo Step 2: Running migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo Failed to run migrations!
    pause
    exit /b 1
)
echo.

echo Step 3: Checking database connection...
php artisan db:show
echo.

echo ========================================
echo Database setup completed successfully!
echo ========================================
pause
