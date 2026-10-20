#!/bin/bash
# ==============================================================================
# Skrip Backup & Deploy Aman SPMB KNBI (CWP / User Directory)
# Target: /home/adminknbi/spmb.smkknbi.sch.id
# ==============================================================================

set -e

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
USER_DIR="/home/adminknbi"
TARGET_DIR="$USER_DIR/spmb.smkknbi.sch.id"
BACKUP_DIR="$USER_DIR/backups_spmb"

echo "========================================================"
echo "  MEMULAI PROSES BACKUP & DEPLOY AMAN SPMB.SMKKNBI.SCH.ID"
echo "  Waktu: $TIMESTAMP"
echo "========================================================"

mkdir -p "$BACKUP_DIR"

# 1. BACKUP DATABASE LAMA
echo "--> [1/5] Memeriksa dan mem-backup database lama..."
if [ -f "$TARGET_DIR/.env" ]; then
    OLD_DB=$(grep "^DB_DATABASE=" "$TARGET_DIR/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'" || true)
    OLD_USER=$(grep "^DB_USERNAME=" "$TARGET_DIR/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'" || true)
    OLD_PASS=$(grep "^DB_PASSWORD=" "$TARGET_DIR/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'" || true)
    
    if [ ! -z "$OLD_DB" ]; then
        echo "    Ditemukan database lama: $OLD_DB. Sedang membuat backup dump..."
        if [ ! -z "$OLD_PASS" ]; then
            mysqldump -u"$OLD_USER" -p"$OLD_PASS" "$OLD_DB" | gzip > "$BACKUP_DIR/db_${OLD_DB}_backup_$TIMESTAMP.sql.gz" || true
        else
            mysqldump -u"$OLD_USER" "$OLD_DB" | gzip > "$BACKUP_DIR/db_${OLD_DB}_backup_$TIMESTAMP.sql.gz" || true
        fi
        echo "    [OK] Database lama berhasil dibackup ke: $BACKUP_DIR/db_${OLD_DB}_backup_$TIMESTAMP.sql.gz"
    fi
fi

# 2. BACKUP SELURUH FILE WEBSITE LAMA
echo "--> [2/5] Mengarsipkan seluruh file web lama..."
if [ -d "$TARGET_DIR" ]; then
    tar -czf "$BACKUP_DIR/files_spmb_lama_$TIMESTAMP.tar.gz" -C "$USER_DIR" "spmb.smkknbi.sch.id"
    echo "    [OK] File website lama aman tersimpan di: $BACKUP_DIR/files_spmb_lama_$TIMESTAMP.tar.gz"
else
    echo "    [INFO] Direktori $TARGET_DIR belum ada, melewati proses archive."
fi

# 3. PERSIAPKAN PROJECT BARU DI FOLDER TERPISAH
echo "--> [3/5] Mengunduh project SPMB KNBI versi terbaru dari GitHub..."
TEMP_NEW_DIR="$USER_DIR/spmb_v2_temp"
rm -rf "$TEMP_NEW_DIR"
git clone https://github.com/wahyuzetha/spmbknbi.git "$TEMP_NEW_DIR"

cd "$TEMP_NEW_DIR"

# Copy file .env dari yang lama jika ada, atau buat yang baru
if [ -f "$TARGET_DIR/.env" ]; then
    echo "    Menggunakan konfigurasi .env yang sudah ada..."
    cp "$TARGET_DIR/.env" "$TEMP_NEW_DIR/.env"
else
    echo "    Membuat file .env baru dari .env.example..."
    cp .env.example .env
fi

# 4. INSTALASI DEPENDENCIES & BUILD
echo "--> [4/5] Menginstal dependensi Composer & Node.js..."
composer install --no-dev --optimize-autoloader
if command -v npm &> /dev/null; then
    npm install
    npm run build
fi

php artisan key:generate --force || true
php artisan storage:link || true
php artisan optimize:clear || true

# 5. SWITCH FOLDER SECARA AMAN (ATOMIC SWAP)
echo "--> [5/5] Mengaktifkan website baru..."
if [ -d "$TARGET_DIR" ]; then
    mv "$TARGET_DIR" "$TARGET_DIR.old_$TIMESTAMP"
fi
mv "$TEMP_NEW_DIR" "$TARGET_DIR"

# Perbaiki kepemilikan file untuk user adminknbi
chown -R adminknbi:adminknbi "$TARGET_DIR"
chmod -R 775 "$TARGET_DIR/storage" "$TARGET_DIR/bootstrap/cache"

echo "========================================================"
echo "  SELESAI! SPMB BARU BERHASIL DI-DEPLOY"
echo "========================================================"
echo "Catatan penting:"
echo "1. Backup file lama Anda ada di: $BACKUP_DIR"
echo "2. Konfigurasi document root di Web Panel (CWP/vhost) harus mengarah ke:"
echo "   $TARGET_DIR/public"
echo "========================================================"
