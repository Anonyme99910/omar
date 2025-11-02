@echo off
echo ========================================
echo   Pushing to GitHub
echo ========================================
echo.

cd /d c:\xampp\htdocs\parfumes

echo Adding all files...
git add .

echo.
echo Committing...
git commit -m "Complete perfume store system with Arabic docs"

echo.
echo Adding remote...
git remote add origin https://github.com/Anonyme99910/parfumefinal.git

echo.
echo Pushing to GitHub...
git branch -M main
git push -u origin main

echo.
echo ========================================
echo   Push Complete!
echo ========================================
echo.
pause
