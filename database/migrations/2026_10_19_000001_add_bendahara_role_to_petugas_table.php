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
        if (Schema::hasTable('petugas')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE petugas MODIFY COLUMN role ENUM('admin', 'panitia', 'bendahara') NOT NULL DEFAULT 'panitia'");
            } else {
                Schema::table('petugas', function (Blueprint $table) {
                    $table->string('role', 30)->default('panitia')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('petugas')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE petugas MODIFY COLUMN role ENUM('admin', 'panitia') NOT NULL DEFAULT 'panitia'");
            }
        }
    }
};
