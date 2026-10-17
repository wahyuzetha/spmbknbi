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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 30)->unique(); // SPMB-2027-0001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('major_id')->constrained('majors')->cascadeOnDelete();
            $table->foreignId('major_choice_2_id')->nullable()->constrained('majors')->nullOnDelete();
            
            $table->enum('registration_type', ['siswa_baru', 'pindahan'])->default('siswa_baru');
            $table->string('recommendation_by')->nullable();
            
            $table->enum('status', [
                'draft',
                'pending',       // Menunggu Verifikasi
                'revision',      // Butuh Revisi Berkas/Data
                'verified',      // Berkas Terverifikasi
                'accepted',      // Diterima
                'rejected'       // Ditolak
            ])->default('pending')->index();
            
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamp('printed_card_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
