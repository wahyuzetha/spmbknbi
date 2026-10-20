#!/bin/bash
# ==============================================================================
# Script Setup & Deploy Laravel 13 untuk Hostinger VPS (AlmaLinux 8)
# Repository: https://github.com/wahyuzetha/spmbknbi.git
# ==============================================================================

set -e

echo "=== [1/9] Update Sistem & Tambah Repositori (EPEL & Remi) ==="
dnf update -y
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf install -y yum-utils policycoreutils-python-utils git curl unzip

echo "=== [2/9] Instalasi & Konfigurasi PHP 8.3 ==="
dnf module reset php -y
dnf module enable php:remi-8.3 -y
dnf install -y php php-fpm php-cli php-mysqlnd php-gd php-curl php-zip \
    php-xml php-mbstring php-bcmath php-intl php-json php-tokenizer php-opcache

# Konfigurasi PHP-FPM untuk Nginx
sed -i 's/user = apache/user = nginx/g' /etc/php-fpm.d/www.conf
sed -i 's/group = apache/group = nginx/g' /etc/php-fpm.d/www.conf
sed -i 's/;listen.owner = nobody/listen.owner = nginx/g' /etc/php-fpm.d/www.conf
sed -i 's/;listen.group = nobody/listen.group = nginx/g' /etc/php-fpm.d/www.conf
sed -i 's/;listen.mode = 0660/listen.mode = 0660/g' /etc/php-fpm.d/www.conf

systemctl enable --now php-fpm

echo "=== [3/9] Instalasi Composer & Node.js 20 ==="
# Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi

# Node.js 20
if ! command -v node &> /dev/null; then
    curl -fsSL https://rpm.nodesource.com/setup_20.x | bash -
    dnf install -y nodejs
fi

echo "=== [4/9] Instalasi Nginx & MariaDB ==="
dnf install -y nginx mariadb-server
systemctl enable --now nginx
systemctl enable --now mariadb

echo "=== [5/9] Konfigurasi Firewall ==="
firewall-cmd --permanent --add-service=http || true
firewall-cmd --permanent --add-service=https || true
firewall-cmd --reload || true

echo "=== [6/9] Setup Direktori Project Laravel ==="
mkdir -p /var/www
if [ ! -d "/var/www/spmbknbi" ]; then
    git clone https://github.com/wahyuzetha/spmbknbi.git /var/www/spmbknbi
fi

cd /var/www/spmbknbi
git config --global --add safe.directory /var/www/spmbknbi
git pull origin main

echo "=== [7/9] Instalasi Dependensi & Build Asset ==="
composer install --no-dev --optimize-autoloader
npm install
npm run build

echo "=== [8/9] Konfigurasi Permission & SELinux ==="
chown -R nginx:nginx /var/www/spmbknbi
chmod -R 775 /var/www/spmbknbi/storage
chmod -R 775 /var/www/spmbknbi/bootstrap/cache

# SELinux Rules agar Nginx & PHP-FPM dapat mengakses database & menulis file storage
setsebool -P httpd_can_network_connect 1 || true
setsebool -P httpd_can_network_connect_db 1 || true
semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/spmbknbi/storage(/.*)?" || true
semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/spmbknbi/bootstrap/cache(/.*)?" || true
restorecon -Rv /var/www/spmbknbi || true

echo "=== [9/9] Selesai Persiapan Awal! ==="
echo "Langkah selanjutnya:"
echo "1. Buat Database MySQL & User"
echo "2. Konfigurasi file /var/www/spmbknbi/.env"
echo "3. Jalankan migrasi: php artisan migrate --seed --force"
echo "4. Konfigurasi Virtualhost Nginx & SSL Certbot"
