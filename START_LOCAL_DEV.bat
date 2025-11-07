@echo off
color 0A
title Parfumes System - Local Development

echo.
echo ========================================
echo   Parfumes System - Local Development
echo   نظام إدارة متجر العطور
echo ========================================
echo.

cd /d c:\xampp\htdocs\parfumes\frontend

echo [1/4] Checking Node.js installation...
node --version >nul 2>&1
if errorlevel 1 (
    color 0C
    echo.
    echo ❌ ERROR: Node.js is NOT installed!
    echo.
    echo Please install Node.js first:
    echo 1. Go to: https://nodejs.org/
    echo 2. Download LTS version
    echo 3. Install with default settings
    echo 4. Restart this script
    echo.
    pause
    exit /b 1
)

echo    ✓ Node.js found: 
node --version
echo.

echo [2/4] Checking npm...
npm --version >nul 2>&1
if errorlevel 1 (
    color 0C
    echo    ❌ npm not found!
    pause
    exit /b 1
)
echo    ✓ npm version: 
npm --version
echo.

echo [3/4] Checking dependencies...
if not exist "node_modules\" (
    echo    Installing dependencies (this may take a few minutes)...
    call npm install
    if errorlevel 1 (
        color 0C
        echo    ❌ npm install failed!
        pause
        exit /b 1
    )
    echo    ✓ Dependencies installed successfully!
) else (
    echo    ✓ Dependencies already installed
)
echo.

echo [4/4] Starting Vite Dev Server...
echo.
echo ========================================
echo   🚀 Server Starting...
echo ========================================
echo.
echo   Frontend: http://localhost:5173
echo   Backend:  http://localhost/parfumes/backend/public
echo.
echo   ⚠️  DO NOT close this window!
echo   ⚠️  Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

call npm run dev

echo.
echo Server stopped.
pause
