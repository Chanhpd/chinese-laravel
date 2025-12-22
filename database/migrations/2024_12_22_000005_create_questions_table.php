<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_part_id');
            $table->unsignedBigInteger('question_type_id');
            $table->integer('order')->default(0);
            
            // General content (shared across all sub-questions)
            $table->json('g_text')->nullable(); // Array of text
            $table->json('g_text_translate')->nullable(); // Object with language keys
            $table->text('g_text_audio')->nullable();
            $table->json('g_text_audio_translate')->nullable();
            $table->json('g_audio')->nullable(); // Array of audio URLs
            $table->json('g_image')->nullable(); // Array of image URLs
            
            $table->integer('total_score')->default(0);
            $table->timestamps();
            
            $table->foreign('exam_part_id')->references('id')->on('exam_parts')->onDelete('cascade');
            $table->foreign('question_type_id')->references('id')->on('question_types')->onDelete('cascade');
            
            $table->index('exam_part_id');
            $table->index('question_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
