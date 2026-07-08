@echo off
cd /d "%~dp0"
set PATH=C:\laragon\bin\php\php-8.5.6-Win32-vs17-x64;C:\laragon\bin\composer;%PATH%
echo ===================================================
echo   Menjalankan HidroNutri (Laravel + Vite + SQLite)
echo ===================================================
echo.
echo Menjalankan Laravel Server (php artisan serve)...
start "Laravel Server" /d "%~dp0" cmd /k "php artisan serve"
echo Menjalankan Vite Dev Server (npm run dev)...
start "Vite Dev Server" /d "%~dp0" cmd /k "npm run dev"
echo.
echo Server backend dan frontend telah dijalankan di jendela terpisah!
echo Buka browser Anda di: http://127.0.0.1:8000
echo.
pause
