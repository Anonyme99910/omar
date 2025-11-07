@echo off
echo ========================================
echo Complete MySQL Recovery for XAMPP
echo ========================================
echo.
echo This will fix common MySQL startup issues:
echo - Corrupted InnoDB log files
echo - Port conflicts
echo - Missing directories
echo - Permission issues
echo.
pause

echo Step 1: Stopping MySQL completely...
net stop mysql 2>nul
taskkill /F /IM mysqld.exe 2>nul
timeout /t 3 /nobreak >nul
echo Done.
echo.

echo Step 2: Freeing port 3306...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :3306 ^| findstr LISTENING') do (
    echo Killing process on port 3306: PID %%a
    taskkill /F /PID %%a 2>nul
)
timeout /t 2 /nobreak >nul
echo Done.
echo.

echo Step 3: Creating backup of InnoDB files...
if not exist "C:\xampp\mysql\backup" mkdir "C:\xampp\mysql\backup"
xcopy "C:\xampp\mysql\data\ib_logfile*" "C:\xampp\mysql\backup\" /Y 2>nul
xcopy "C:\xampp\mysql\data\ibdata1" "C:\xampp\mysql\backup\" /Y 2>nul
echo Done.
echo.

echo Step 4: Removing corrupted InnoDB log files...
del /F /Q "C:\xampp\mysql\data\ib_logfile0" 2>nul
del /F /Q "C:\xampp\mysql\data\ib_logfile1" 2>nul
echo Done.
echo.

echo Step 5: Cleaning temporary files...
del /F /Q "C:\xampp\mysql\data\*.pid" 2>nul
del /F /Q "C:\xampp\mysql\data\*.err.old" 2>nul
del /F /Q "C:\xampp\tmp\mysql.sock" 2>nul
echo Done.
echo.

echo Step 6: Ensuring required directories exist...
if not exist "C:\xampp\tmp" mkdir "C:\xampp\tmp"
if not exist "C:\xampp\mysql\data" mkdir "C:\xampp\mysql\data"
echo Done.
echo.

echo Step 7: Setting permissions on MySQL data directory...
icacls "C:\xampp\mysql\data" /grant Everyone:(OI)(CI)F /T >nul 2>&1
echo Done.
echo.

echo ========================================
echo MySQL Recovery Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Go to XAMPP Control Panel
echo 2. Click START for MySQL
echo 3. If it fails, click "Logs" to see the error
echo.
echo Common solutions if it still fails:
echo - Check if port 3306 is used by another program
echo - Run XAMPP Control Panel as Administrator
echo - Check C:\xampp\mysql\data\mysql_error.log
echo.
pause
