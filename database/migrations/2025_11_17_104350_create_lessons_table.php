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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->timestamps();

            $table->index('course_id');
            $table->index(['course_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
