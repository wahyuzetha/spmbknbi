@echo off
title SPMB SMK Karya Nugraha Boyolali
echo ========================================================
echo  MEMULAI SISTEM SPMB SMK KARYA NUGRAHA BOYOLALI
echo ========================================================

:: 1. Cek & Jalankan MySQL jika belum aktif
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Database MySQL sudah berjalan aktif.
) else (
    echo [..] Menjalankan MySQL dari C:\xamppqu...
    start /B "" "C:\xamppqu\mysql\bin\mysqld.exe" --defaults-file="C:\xamppqu\mysql\bin\my.ini" --standalone
    timeout /t 2 >nul
    echo [OK] Database MySQL berhasil diaktifkan.
)

:: 2. Pastikan symlink storage Laravel terhubung
php artisan storage:link 2>nul

:: 3. Jalankan server Laravel
echo.
echo ========================================================
echo  Server SPMB berjalan di: http://127.0.0.1:8000
echo  Akses Admin: http://127.0.0.1:8000/login
echo  Tekan Ctrl+C untuk menghentikan server.
echo ========================================================
echo.
php artisan serve --host=127.0.0.1 --port=8000
