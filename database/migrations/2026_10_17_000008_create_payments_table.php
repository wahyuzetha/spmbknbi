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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            
            $table->string('invoice_number', 40)->unique();
            $table->enum('payment_type', [
                'pendaftaran',
                'daftar_ulang',
                'seragam',
                'gedung',
                'lainnya'
            ])->default('pendaftaran');
            
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 30)->default('tunai'); // tunai, transfer, qris
            $table->string('proof_file')->nullable();
            
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending')->index();
            $table->text('notes')->nullable();
            
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
