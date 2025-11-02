@echo off
echo ========================================
echo   Git Cleanup - Make Everything Green
echo ========================================
echo.

REM Check if git is initialized
if not exist .git (
    echo Initializing git repository...
    git init
    echo.
)

echo Removing cached files...
git rm -r --cached . 2>nul

echo.
echo Adding files according to .gitignore...
git add .

echo.
echo Checking status...
git status --short

echo.
echo ========================================
echo   Git Cleanup Complete!
echo ========================================
echo.
echo All files should now show as green (new) or clean.
echo.
echo To commit:
echo   git commit -m "Clean project - production ready"
echo.
pause
