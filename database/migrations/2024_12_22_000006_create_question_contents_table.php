<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->integer('sub_order')->default(0); // For questions with multiple parts
            
            // Question content
            $table->text('q_text')->nullable();
            $table->text('q_audio')->nullable();
            $table->text('q_image')->nullable();
            
            // Answer options
            $table->json('a_text')->nullable(); // Array of answer texts
            $table->json('a_audio')->nullable(); // Array of answer audio URLs
            $table->json('a_image')->nullable(); // Array of answer image URLs
            
            // Correct answers
            $table->json('a_correct')->nullable(); // Array of correct answer indices
            $table->json('a_more_correct')->nullable(); // Additional correct answers
            
            // Explanations
            $table->json('explain')->nullable(); // Explanations by language
            $table->json('advance_explain')->nullable(); // Advanced explanations
            $table->json('lang_explain_advance')->nullable();
            
            $table->integer('score')->default(5);
            $table->timestamps();
            
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_contents');
    }
};
