@echo off
echo ========================================
echo Fixing Windows Hosts File for localhost
echo ========================================
echo.
echo This will uncomment localhost entries in hosts file
echo Run this script as Administrator!
echo.
pause

echo Backing up hosts file...
copy C:\Windows\System32\drivers\etc\hosts C:\Windows\System32\drivers\etc\hosts.backup
echo.

echo Fixing localhost entries...
powershell -Command "(Get-Content C:\Windows\System32\drivers\etc\hosts) -replace '#\s*127\.0\.0\.1\s+localhost', '127.0.0.1       localhost' -replace '#\s*::1\s+localhost', '::1             localhost' | Set-Content C:\Windows\System32\drivers\etc\hosts"
echo.

echo Flushing DNS cache...
ipconfig /flushdns
echo.

echo ========================================
echo Hosts file fixed!
echo localhost now resolves to 127.0.0.1
echo ========================================
pause
