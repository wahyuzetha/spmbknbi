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
        if (! Schema::hasTable('pengeluaran')) {
            Schema::create('pengeluaran', function (Blueprint $table) {
                $table->id();
                $table->string('expense_code', 50)->unique();
                $table->string('title');
                $table->string('category', 100)->nullable()->default('Operasional SPMB');
                $table->decimal('amount', 12, 2)->default(0);
                $table->date('expense_date');
                $table->text('description'); // Keterangan penggunaan uang calon siswa pendaftar
                $table->string('recipient', 255)->nullable(); // Penerima / Toko / PJ
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('petugas')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
