@echo off
setlocal
cd /d "%~dp0"
title SeekSaveAnd - Local Server

echo ==========================================
echo        SeekSaveAnd - Local Launcher
echo ==========================================
echo.

where php >nul 2>nul
if errorlevel 1 (
  echo ERROR: PHP was not found. Install PHP 8.2 or newer and add it to PATH.
  echo If you use XAMPP, add its php folder to your Windows PATH.
  pause
  exit /b 1
)

if not exist ".env" (
  copy ".env.example" ".env" >nul
  echo Created .env from .env.example.
)

if not exist "vendor\autoload.php" (
  where composer >nul 2>nul
  if errorlevel 1 (
    echo ERROR: Composer is required because vendor dependencies are missing.
    pause
    exit /b 1
  )
  composer install
  if errorlevel 1 (
    echo ERROR: Composer could not install dependencies.
    pause
    exit /b 1
  )
)

rem Keep Laravel's generated bootstrap cache out of protected folders such as OneDrive.
set "LARAVEL_CACHE_DIR=%TEMP%\SeekSaveAnd-cache"
if not exist "%LARAVEL_CACHE_DIR%" mkdir "%LARAVEL_CACHE_DIR%"
if not exist "%LARAVEL_CACHE_DIR%\" (
  echo ERROR: Could not create the Laravel cache directory: "%LARAVEL_CACHE_DIR%"
  pause
  exit /b 1
)
> "%LARAVEL_CACHE_DIR%\write-test.tmp" echo writable
if errorlevel 1 (
  echo ERROR: The Laravel cache directory is not writable: "%LARAVEL_CACHE_DIR%"
  pause
  exit /b 1
)
del "%LARAVEL_CACHE_DIR%\write-test.tmp" >nul 2>nul
set "APP_PACKAGES_CACHE=%LARAVEL_CACHE_DIR%\packages.php"
set "APP_SERVICES_CACHE=%LARAVEL_CACHE_DIR%\services.php"
set "APP_CONFIG_CACHE=%LARAVEL_CACHE_DIR%\config.php"
set "APP_ROUTES_CACHE=%LARAVEL_CACHE_DIR%\routes.php"
set "APP_EVENTS_CACHE=%LARAVEL_CACHE_DIR%\events.php"

findstr /R /C:"^APP_KEY=$" ".env" >nul
if not errorlevel 1 php artisan key:generate --force

php artisan optimize:clear
if errorlevel 1 (
  echo ERROR: Laravel could not boot. Check PHP extensions and the error above.
  pause
  exit /b 1
)

php artisan migrate --force
if errorlevel 1 (
  echo ERROR: Laravel migration failed. Review the error above and check the database configuration and required PHP extensions.
  pause
  exit /b 1
)

if not exist "public\storage" php artisan storage:link

echo.
echo Opening the app in Chrome at http://localhost:8000/login
start "" /min cmd /c "timeout /t 3 >nul & start "" chrome http://localhost:8000/login"
echo.
echo Keep this window open while using SeekSaveAnd.
echo Press Ctrl+C to stop the server.
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
