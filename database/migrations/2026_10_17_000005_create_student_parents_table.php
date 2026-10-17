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
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            
            // Data Ayah
            $table->string('nama_ayah');
            $table->string('status_ayah', 20)->default('hidup'); // hidup, meninggal
            $table->string('nik_ayah', 16)->nullable();
            $table->string('pekerjaan_ayah', 60)->nullable();
            $table->string('pendidikan_ayah', 30)->nullable();
            $table->string('penghasilan_ayah', 50)->nullable();
            $table->string('no_hp_ayah', 20)->nullable();
            
            // Data Ibu
            $table->string('nama_ibu');
            $table->string('status_ibu', 20)->default('hidup'); // hidup, meninggal
            $table->string('nik_ibu', 16)->nullable();
            $table->string('pekerjaan_ibu', 60)->nullable();
            $table->string('pendidikan_ibu', 30)->nullable();
            $table->string('penghasilan_ibu', 50)->nullable();
            $table->string('no_hp_ibu', 20)->nullable();
            
            // Data Wali (Opsional)
            $table->string('nama_wali')->nullable();
            $table->string('hubungan_wali', 30)->nullable();
            $table->string('pekerjaan_wali', 60)->nullable();
            $table->string('no_hp_wali', 20)->nullable();
            $table->text('alamat_wali')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
