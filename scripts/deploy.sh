#!/bin/bash
# ==============================================================================
# Script Quick Deploy / Update Laravel SPMB KNBI di VPS
# ==============================================================================

set -e

PROJECT_DIR="/var/www/spmbknbi"

echo "=== Memulai Update Project SPMB KNBI ==="
cd "$PROJECT_DIR"

# Aktifkan maintenance mode sementara
php artisan down || true

# Tarik perubahan terbaru dari GitHub
git pull origin main

# Update dependencies & build asset
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Migrasi database jika ada tabel baru
php artisan migrate --force

# Bersihkan dan buat cache konfigurasi untuk performa maksimal
php artisan optimize:clear
php artisan optimize
php artisan view:cache

# Perbaiki permission folder storage
chown -R nginx:nginx storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Matikan maintenance mode
php artisan up

# Restart PHP-FPM untuk memuat OPcache terbaru
systemctl restart php-fpm

echo "=== Deployment Berhasil & Web Kembali Online! ==="
