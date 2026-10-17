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
        Schema::create('student_biodatas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            
            $table->string('nik', 16)->nullable()->index();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('agama', 30);
            $table->string('tempat_lahir', 60);
            $table->date('tanggal_lahir');
            $table->string('no_hp', 20);
            $table->string('email')->nullable();
            
            // Alamat Domisili
            $table->text('alamat_jalan');
            $table->string('desa', 60);
            $table->string('kecamatan', 60);
            $table->string('kabupaten', 60);
            $table->string('provinsi', 60)->default('Jawa Tengah');
            $table->string('kode_pos', 10)->nullable();
            
            // Tambahan fisik/keluarga
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->unsignedSmallInteger('tinggi_badan')->nullable(); // dalam cm
            $table->unsignedSmallInteger('berat_badan')->nullable(); // dalam kg
            $table->string('riwayat_penyakit')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_biodatas');
    }
};
