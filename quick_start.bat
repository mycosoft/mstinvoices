@echo off
title MST Invoice Generator - Quick Start
color 0A

echo ========================================
echo   MST Invoice Generator
echo   By Mycosoft Technologies
echo ========================================
echo.

:: Check if we're in the correct directory
if not exist "artisan" (
    echo ERROR: This script must be run from the Laravel project root directory!
    pause
    exit /b 1
)

echo Starting Laravel development server...
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.

:: Start the Laravel development server
php artisan serve --host=127.0.0.1 --port=8000

pause
