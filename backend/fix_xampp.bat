@echo off
echo ========================================
echo XAMPP Port Conflict Fix
echo ========================================
echo.

echo Step 1: Stopping XAMPP services...
taskkill /F /IM httpd.exe 2>nul
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 /nobreak >nul
echo Done.
echo.

echo Step 2: Freeing port 443 conflicts...
echo Finding processes on port 443...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :443 ^| findstr LISTENING') do (
    echo Killing PID %%a
    taskkill /F /PID %%a 2>nul
)
echo Done.
echo.

echo Step 3: Freeing port 80 conflicts...
echo Finding processes on port 80...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :80 ^| findstr LISTENING') do (
    echo Killing PID %%a
    taskkill /F /PID %%a 2>nul
)
echo Done.
echo.

echo Step 4: Cleaning MySQL crash recovery...
del /Q "C:\xampp\mysql\data\ib_logfile*" 2>nul
echo Done.
echo.

echo ========================================
echo Port cleanup complete!
echo Now start Apache and MySQL from XAMPP Control Panel
echo Then try: http://localhost/phpmyadmin/
echo ========================================
pause
