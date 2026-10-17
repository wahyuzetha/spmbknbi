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
        Schema::create('student_schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            
            $table->string('asal_sekolah'); // contoh: SMPN 1 Boyolali
            $table->string('npsn_sekolah', 20)->nullable();
            $table->string('jenis_sekolah', 30)->default('SMP'); // SMP, MTs, Paket B, Lainnya
            $table->string('status_sekolah', 20)->default('Negeri'); // Negeri, Swasta
            $table->string('kabupaten_sekolah', 60)->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('no_ijazah', 50)->nullable();
            $table->decimal('nilai_rata_raport', 5, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_schools');
    }
};
