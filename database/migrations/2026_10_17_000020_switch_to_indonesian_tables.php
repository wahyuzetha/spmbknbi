<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bersihkan tabel baru jika ada sisa eksekusi migrasi yang gagal sebelumnya
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('dokumen_siswa');
        Schema::dropIfExists('asal_sekolah_siswa');
        Schema::dropIfExists('orang_tua_siswa');
        Schema::dropIfExists('biodata_siswa');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pendaftaran');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('tahun_ajaran');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('petugas');
        Schema::dropIfExists('pengaturan_sistem');
        Schema::enableForeignKeyConstraints();

        // 1. Tabel Petugas (Admin & Panitia SPMB)
        if (! Schema::hasTable('petugas')) {
            Schema::create('petugas', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username', 100)->unique()->nullable();
                $table->string('email')->unique();
                $table->string('phone', 20)->nullable();
                $table->enum('role', ['admin', 'panitia'])->default('panitia')->index();
                $table->boolean('is_active')->default(true);
                $table->string('avatar')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Tabel Siswa (Calon Siswa / Murid Pendaftar Baru)
        if (! Schema::hasTable('siswa')) {
            Schema::create('siswa', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('nisn', 10)->unique()->nullable();
                $table->string('phone', 20)->unique()->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role', 20)->default('murid')->index();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 3. Tabel Tahun Ajaran
        if (! Schema::hasTable('tahun_ajaran')) {
            Schema::create('tahun_ajaran', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->boolean('is_active')->default(true);
                $table->date('registration_start')->nullable();
                $table->date('registration_end')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. Tabel Jurusan
        if (! Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name');
                $table->string('industry_class', 100)->nullable();
                $table->integer('quota')->default(100);
                $table->text('description')->nullable();
                $table->string('icon', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Tabel Pendaftaran
        if (! Schema::hasTable('pendaftaran')) {
            Schema::create('pendaftaran', function (Blueprint $table) {
                $table->id();
                $table->string('registration_number', 50)->unique();
                $table->unsignedBigInteger('user_id'); // Merujuk ke siswa.id
                $table->unsignedBigInteger('academic_year_id'); // Merujuk ke tahun_ajaran.id
                $table->unsignedBigInteger('major_id'); // Merujuk ke jurusan.id
                $table->unsignedBigInteger('major_choice_2_id')->nullable();
                $table->string('registration_type', 50)->default('siswa_baru');
                $table->string('recommendation_by')->nullable();
                $table->enum('status', ['pending', 'verified', 'accepted', 'rejected'])->default('pending')->index();
                $table->unsignedBigInteger('verified_by')->nullable(); // Merujuk ke petugas.id
                $table->timestamp('verified_at')->nullable();
                $table->text('verification_notes')->nullable();
                $table->timestamp('printed_card_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Tabel Biodata Siswa
        if (! Schema::hasTable('biodata_siswa')) {
            Schema::create('biodata_siswa', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->unique();
                $table->string('nik', 16)->nullable();
                $table->string('no_kk', 16)->nullable();
                $table->string('nama_lengkap');
                $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
                $table->string('agama', 50)->default('Islam');
                $table->string('tempat_lahir', 100)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('no_hp', 20)->nullable();
                $table->string('email')->nullable();
                $table->text('alamat_jalan')->nullable();
                $table->string('desa', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('kabupaten', 100)->default('Boyolali');
                $table->string('provinsi', 100)->default('Jawa Tengah');
                $table->string('kode_pos', 10)->nullable();
                $table->integer('anak_ke')->nullable();
                $table->integer('jumlah_saudara')->nullable();
                $table->integer('tinggi_badan')->nullable();
                $table->integer('berat_badan')->nullable();
                $table->string('riwayat_penyakit')->nullable();
                $table->timestamps();
            });
        }

        // 7. Tabel Orang Tua Siswa
        if (! Schema::hasTable('orang_tua_siswa')) {
            Schema::create('orang_tua_siswa', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->unique();
                $table->string('nama_ayah')->nullable();
                $table->string('status_ayah', 20)->default('hidup');
                $table->string('nik_ayah', 16)->nullable();
                $table->string('pekerjaan_ayah', 100)->nullable();
                $table->string('pendidikan_ayah', 50)->nullable();
                $table->string('penghasilan_ayah', 100)->nullable();
                $table->string('no_hp_ayah', 20)->nullable();
                $table->string('nama_ibu')->nullable();
                $table->string('status_ibu', 20)->default('hidup');
                $table->string('nik_ibu', 16)->nullable();
                $table->string('pekerjaan_ibu', 100)->nullable();
                $table->string('pendidikan_ibu', 50)->nullable();
                $table->string('penghasilan_ibu', 100)->nullable();
                $table->string('no_hp_ibu', 20)->nullable();
                $table->string('nama_wali')->nullable();
                $table->string('hubungan_wali', 50)->nullable();
                $table->string('pekerjaan_wali', 100)->nullable();
                $table->string('no_hp_wali', 20)->nullable();
                $table->text('alamat_wali')->nullable();
                $table->timestamps();
            });
        }

        // 8. Tabel Asal Sekolah Siswa
        if (! Schema::hasTable('asal_sekolah_siswa')) {
            Schema::create('asal_sekolah_siswa', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id')->unique();
                $table->string('asal_sekolah');
                $table->string('npsn_sekolah', 20)->nullable();
                $table->string('jenis_sekolah', 50)->default('SMP');
                $table->string('status_sekolah', 20)->default('Negeri');
                $table->string('kabupaten_sekolah', 100)->nullable();
                $table->year('tahun_lulus')->nullable();
                $table->string('no_ijazah', 100)->nullable();
                $table->decimal('nilai_rata_raport', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        // 9. Tabel Dokumen Siswa
        if (! Schema::hasTable('dokumen_siswa')) {
            Schema::create('dokumen_siswa', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id');
                $table->string('document_type', 50);
                $table->string('file_path');
                $table->string('original_filename')->nullable();
                $table->integer('file_size')->nullable();
                $table->string('mime_type', 50)->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 10. Tabel Pembayaran
        if (! Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('registration_id');
                $table->string('invoice_number', 50)->unique();
                $table->string('payment_type', 50)->default('pendaftaran');
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('payment_method', 50)->default('cash');
                $table->string('proof_file')->nullable();
                $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamps();
            });
        }

        // 11. Tabel Pengaturan Sistem
        if (! Schema::hasTable('pengaturan_sistem')) {
            Schema::create('pengaturan_sistem', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('group', 50)->default('general');
                $table->string('type', 50)->default('text');
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }

        // SINKRONISASI DATA DARI TABEL LAMA KE TABEL BARU
        // A. Salin Admin & Panitia ke `petugas`
        if (Schema::hasTable('users')) {
            $staff = DB::table('users')->whereIn('role', ['admin', 'panitia'])->get();
            foreach ($staff as $s) {
                DB::table('petugas')->updateOrInsert(
                    ['email' => $s->email],
                    [
                        'name' => $s->name,
                        'username' => $s->username ?? $s->phone ?? 'staff_' . $s->id,
                        'email' => $s->email,
                        'phone' => $s->phone,
                        'role' => $s->role,
                        'is_active' => $s->is_active ?? 1,
                        'avatar' => $s->avatar ?? null,
                        'password' => $s->password,
                        'created_at' => $s->created_at ?? now(),
                        'updated_at' => $s->updated_at ?? now(),
                    ]
                );
            }

            // B. Salin Murid ke `siswa`
            $students = DB::table('users')->where('role', 'murid')->get();
            foreach ($students as $st) {
                DB::table('siswa')->updateOrInsert(
                    ['email' => $st->email],
                    [
                        'id' => $st->id,
                        'name' => $st->name,
                        'nisn' => $st->nisn,
                        'phone' => $st->phone,
                        'email' => $st->email,
                        'password' => $st->password,
                        'role' => 'murid',
                        'is_active' => $st->is_active ?? 1,
                        'created_at' => $st->created_at ?? now(),
                        'updated_at' => $st->updated_at ?? now(),
                    ]
                );
            }
        }

        // C. Salin Tahun Ajaran
        if (Schema::hasTable('academic_years')) {
            $years = DB::table('academic_years')->get();
            foreach ($years as $y) {
                DB::table('tahun_ajaran')->updateOrInsert(
                    ['id' => $y->id],
                    (array) $y
                );
            }
        }

        // D. Salin Jurusan
        if (Schema::hasTable('majors')) {
            $majors = DB::table('majors')->get();
            foreach ($majors as $m) {
                DB::table('jurusan')->updateOrInsert(
                    ['id' => $m->id],
                    (array) $m
                );
            }
        }

        // E. Salin Pendaftaran
        if (Schema::hasTable('registrations')) {
            $regs = DB::table('registrations')->get();
            foreach ($regs as $r) {
                DB::table('pendaftaran')->updateOrInsert(
                    ['id' => $r->id],
                    (array) $r
                );
            }
        }

        // F. Salin Biodata
        if (Schema::hasTable('student_biodatas')) {
            $bios = DB::table('student_biodatas')->get();
            foreach ($bios as $b) {
                DB::table('biodata_siswa')->updateOrInsert(
                    ['id' => $b->id],
                    (array) $b
                );
            }
        }

        // G. Salin Orang Tua
        if (Schema::hasTable('student_parents')) {
            $parents = DB::table('student_parents')->get();
            foreach ($parents as $p) {
                DB::table('orang_tua_siswa')->updateOrInsert(
                    ['id' => $p->id],
                    (array) $p
                );
            }
        }

        // H. Salin Asal Sekolah
        if (Schema::hasTable('student_schools')) {
            $schools = DB::table('student_schools')->get();
            foreach ($schools as $sc) {
                DB::table('asal_sekolah_siswa')->updateOrInsert(
                    ['id' => $sc->id],
                    (array) $sc
                );
            }
        }

        // I. Salin Dokumen
        if (Schema::hasTable('student_documents')) {
            $docs = DB::table('student_documents')->get();
            foreach ($docs as $d) {
                DB::table('dokumen_siswa')->updateOrInsert(
                    ['id' => $d->id],
                    (array) $d
                );
            }
        }

        // J. Salin Pembayaran
        if (Schema::hasTable('payments')) {
            $pays = DB::table('payments')->get();
            foreach ($pays as $py) {
                DB::table('pembayaran')->updateOrInsert(
                    ['id' => $py->id],
                    (array) $py
                );
            }
        }

        // K. Salin Pengaturan Sistem
        if (Schema::hasTable('system_settings')) {
            $sets = DB::table('system_settings')->get();
            foreach ($sets as $st) {
                DB::table('pengaturan_sistem')->updateOrInsert(
                    ['id' => $st->id],
                    (array) $st
                );
            }
        }

        // HAPUS TABEL LAMA BERBAHASA INGGRIS AGAR DATABASE BERSIH
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_schools');
        Schema::dropIfExists('student_parents');
        Schema::dropIfExists('student_biodatas');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
