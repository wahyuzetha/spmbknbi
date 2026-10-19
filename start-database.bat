@echo off
echo ========================================================
echo  Menjalankan Database MySQL (XAMPP C:\xamppqu) SPMB
echo ========================================================

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL Service / mysqld.exe sudah berjalan!
) else (
    echo [..] Memulai mysqld.exe di background...
    start /B "" "C:\xamppqu\mysql\bin\mysqld.exe" --defaults-file="C:\xamppqu\mysql\bin\my.ini" --standalone
    timeout /t 2 >nul
    echo [OK] MySQL berhasil dijalankan.
)

echo.
echo Database SPMB aktif dan siap digunakan.
echo ========================================================
