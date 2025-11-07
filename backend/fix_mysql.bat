@echo off
echo ========================================
echo MySQL Crash Recovery Fix
echo ========================================
echo.

echo Step 1: Stopping any running MySQL processes...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 /nobreak >nul
echo Done.
echo.

echo Step 2: Backing up MySQL data directory...
if not exist "C:\xampp\mysql\data_backup" mkdir "C:\xampp\mysql\data_backup"
xcopy "C:\xampp\mysql\data\ib_logfile*" "C:\xampp\mysql\data_backup\" /Y 2>nul
echo Done.
echo.

echo Step 3: Removing corrupted InnoDB log files...
del /Q "C:\xampp\mysql\data\ib_logfile0" 2>nul
del /Q "C:\xampp\mysql\data\ib_logfile1" 2>nul
del /Q "C:\xampp\mysql\data\ibdata1.bak" 2>nul
echo Done.
echo.

echo Step 4: Checking for port 3306 conflicts...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :3306 ^| findstr LISTENING') do (
    echo Found process on port 3306: PID %%a
    taskkill /F /PID %%a 2>nul
)
echo Done.
echo.

echo Step 5: Cleaning MySQL temp files...
del /Q "C:\xampp\mysql\data\*.err" 2>nul
del /Q "C:\xampp\mysql\data\*.pid" 2>nul
echo Done.
echo.

echo ========================================
echo MySQL Recovery Complete!
echo ========================================
echo.
echo Now try to start MySQL from XAMPP Control Panel.
echo If it still fails, check the error log:
echo C:\xampp\mysql\data\mysql_error.log
echo.
pause
