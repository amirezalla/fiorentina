<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_random')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_for_home')->default(false);
            $table->boolean('is_for_post')->default(false);
            $table->timestamps();
        });
        Schema::create('media_file_video', function (Blueprint $table) {
            $table->foreignId('media_file_id')
                ->references('id')
                ->on('media_files')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('video_id')
                ->references('id')
                ->on('videos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedInteger('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_file_video');
        Schema::dropIfExists('videos');
    }
};
