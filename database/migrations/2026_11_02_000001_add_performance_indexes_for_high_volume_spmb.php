<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimasi indeks tabel pendaftaran (> 500 calon siswa & cetak kartu instan)
        if (Schema::hasTable('pendaftaran')) {
            Schema::table('pendaftaran', function (Blueprint $table) {
                if (! Schema::hasIndex('pendaftaran', ['user_id'])) {
                    $table->index('user_id', 'idx_pendaftaran_user_id');
                }
                if (! Schema::hasIndex('pendaftaran', ['major_id'])) {
                    $table->index('major_id', 'idx_pendaftaran_major_id');
                }
                if (! Schema::hasIndex('pendaftaran', ['academic_year_id'])) {
                    $table->index('academic_year_id', 'idx_pendaftaran_academic_year_id');
                }
                if (! Schema::hasIndex('pendaftaran', ['status', 'created_at'])) {
                    $table->index(['status', 'created_at'], 'idx_pendaftaran_status_created');
                }
                if (! Schema::hasIndex('pendaftaran', ['created_at'])) {
                    $table->index('created_at', 'idx_pendaftaran_created_at');
                }
            });
        }

        // 2. Optimasi indeks tabel dokumen_siswa (lookup pas_foto saat cetak kartu & verifikasi berkas)
        if (Schema::hasTable('dokumen_siswa')) {
            Schema::table('dokumen_siswa', function (Blueprint $table) {
                if (! Schema::hasIndex('dokumen_siswa', ['registration_id', 'document_type'])) {
                    $table->index(['registration_id', 'document_type'], 'idx_dokumen_reg_type');
                }
            });
        }

        // 3. Optimasi indeks tabel pembayaran (rekap kasir, kwitansi & filter status pembayaran)
        if (Schema::hasTable('pembayaran')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                if (! Schema::hasIndex('pembayaran', ['registration_id', 'status'])) {
                    $table->index(['registration_id', 'status'], 'idx_pembayaran_reg_status');
                }
                if (! Schema::hasIndex('pembayaran', ['status', 'payment_date'])) {
                    $table->index(['status', 'payment_date'], 'idx_pembayaran_status_date');
                }
            });
        }

        // 4. Optimasi indeks tabel siswa (pencarian nama, login username/phone/nisn cepat)
        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table) {
                if (! Schema::hasIndex('siswa', ['name'])) {
                    $table->index('name', 'idx_siswa_name');
                }
                if (Schema::hasColumn('siswa', 'username') && ! Schema::hasIndex('siswa', ['username'])) {
                    $table->index('username', 'idx_siswa_username');
                }
            });
        }

        // 5. Optimasi indeks tabel asal_sekolah_siswa (statistik grafik asal sekolah SMP/MTs)
        if (Schema::hasTable('asal_sekolah_siswa')) {
            Schema::table('asal_sekolah_siswa', function (Blueprint $table) {
                if (! Schema::hasIndex('asal_sekolah_siswa', ['asal_sekolah'])) {
                    $table->index('asal_sekolah', 'idx_asal_sekolah_nama');
                }
            });
        }

        // 6. Optimasi indeks tabel tahun_ajaran & jurusan (filter aktif cepat)
        if (Schema::hasTable('tahun_ajaran')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                if (! Schema::hasIndex('tahun_ajaran', ['is_active'])) {
                    $table->index('is_active', 'idx_tahun_ajaran_active');
                }
            });
        }

        if (Schema::hasTable('jurusan')) {
            Schema::table('jurusan', function (Blueprint $table) {
                if (! Schema::hasIndex('jurusan', ['is_active'])) {
                    $table->index('is_active', 'idx_jurusan_active');
                }
            });
        }

        // 7. Optimasi indeks tabel pengeluaran
        if (Schema::hasTable('pengeluaran')) {
            Schema::table('pengeluaran', function (Blueprint $table) {
                if (! Schema::hasIndex('pengeluaran', ['expense_date'])) {
                    $table->index('expense_date', 'idx_pengeluaran_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pendaftaran')) {
            Schema::table('pendaftaran', function (Blueprint $table) {
                foreach (['idx_pendaftaran_user_id', 'idx_pendaftaran_major_id', 'idx_pendaftaran_academic_year_id', 'idx_pendaftaran_status_created', 'idx_pendaftaran_created_at'] as $idx) {
                    if (Schema::hasIndex('pendaftaran', $idx)) {
                        $table->dropIndex($idx);
                    }
                }
            });
        }

        if (Schema::hasTable('dokumen_siswa')) {
            Schema::table('dokumen_siswa', function (Blueprint $table) {
                if (Schema::hasIndex('dokumen_siswa', 'idx_dokumen_reg_type')) {
                    $table->dropIndex('idx_dokumen_reg_type');
                }
            });
        }

        if (Schema::hasTable('pembayaran')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                foreach (['idx_pembayaran_reg_status', 'idx_pembayaran_status_date'] as $idx) {
                    if (Schema::hasIndex('pembayaran', $idx)) {
                        $table->dropIndex($idx);
                    }
                }
            });
        }

        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table) {
                foreach (['idx_siswa_name', 'idx_siswa_username'] as $idx) {
                    if (Schema::hasIndex('siswa', $idx)) {
                        $table->dropIndex($idx);
                    }
                }
            });
        }

        if (Schema::hasTable('asal_sekolah_siswa')) {
            Schema::table('asal_sekolah_siswa', function (Blueprint $table) {
                if (Schema::hasIndex('asal_sekolah_siswa', 'idx_asal_sekolah_nama')) {
                    $table->dropIndex('idx_asal_sekolah_nama');
                }
            });
        }

        if (Schema::hasTable('tahun_ajaran')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                if (Schema::hasIndex('tahun_ajaran', 'idx_tahun_ajaran_active')) {
                    $table->dropIndex('idx_tahun_ajaran_active');
                }
            });
        }

        if (Schema::hasTable('jurusan')) {
            Schema::table('jurusan', function (Blueprint $table) {
                if (Schema::hasIndex('jurusan', 'idx_jurusan_active')) {
                    $table->dropIndex('idx_jurusan_active');
                }
            });
        }

        if (Schema::hasTable('pengeluaran')) {
            Schema::table('pengeluaran', function (Blueprint $table) {
                if (Schema::hasIndex('pengeluaran', 'idx_pengeluaran_date')) {
                    $table->dropIndex('idx_pengeluaran_date');
                }
            });
        }
    }
};
