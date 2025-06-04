@echo off
echo ========================================
echo    UCUA Email Queue Processor
echo ========================================
echo.
echo Choose an option:
echo 1. Process pending emails once (RECOMMENDED)
echo 2. Start continuous queue worker
echo 3. Check queue status
echo.
set /p choice="Enter your choice (1-3): "

if "%choice%"=="1" (
    echo.
    echo Processing pending emails...
    php artisan queue:manage process
    echo.
    echo Done! Check your email inbox.
    pause
) else if "%choice%"=="2" (
    echo.
    echo Starting continuous queue worker...
    echo Keep this window open to process emails!
    echo Press Ctrl+C to stop the worker
    echo.
    php artisan queue:work --timeout=300 --sleep=3 --tries=3
    echo.
    echo Queue worker stopped.
    pause
) else if "%choice%"=="3" (
    echo.
    php artisan queue:manage status
    echo.
    pause
) else (
    echo Invalid choice. Please run again.
    pause
)
