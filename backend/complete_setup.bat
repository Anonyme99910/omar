@echo off
echo ========================================
echo Complete XAMPP + Database Setup
echo ========================================
echo.

echo Step 1: Creating database...
php create_db.php
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Failed to create database!
    echo Make sure MySQL is running in XAMPP.
    pause
    exit /b 1
)
echo Database created successfully!
echo.

echo Step 2: Running Laravel migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Failed to run migrations!
    echo Check your .env database settings.
    pause
    exit /b 1
)
echo Migrations completed successfully!
echo.

echo Step 3: Verifying database connection...
php artisan db:show
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Your database is ready:
echo - Database: perfume_store
echo - Host: 127.0.0.1:3306
echo - User: root
echo - Password: (empty)
echo.
echo All tables have been created.
echo Your app can now save and retrieve data!
echo.
pause
