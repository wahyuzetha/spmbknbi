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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            
            $table->enum('document_type', [
                'kartu_keluarga',
                'akta_kelahiran',
                'ijazah_skl',
                'raport',
                'pas_foto',
                'kartu_kip_pkh',
                'lainnya'
            ])->index();
            
            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedInteger('file_size')->comment('Dalam KB');
            $table->string('mime_type', 60)->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('notes')->nullable(); // Catatan panitia bila berkas buram/salah
            
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
