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
        Schema::table('jurusan', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('code');
            $table->string('tagline')->nullable()->after('description');
            $table->string('head_of_major')->nullable()->after('tagline');
            $table->string('banner_image')->nullable()->after('head_of_major');
            $table->string('video_url')->nullable()->after('banner_image');
            $table->json('advantages')->nullable()->after('video_url');
            $table->json('career_opportunities')->nullable()->after('advantages');
            $table->json('competencies')->nullable()->after('career_opportunities');
            $table->json('facilities')->nullable()->after('competencies');
        });

        Schema::create('jurusan_galeri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurusan_galeri');

        Schema::table('jurusan', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'tagline',
                'head_of_major',
                'banner_image',
                'video_url',
                'advantages',
                'career_opportunities',
                'competencies',
                'facilities',
            ]);
        });
    }
};
