<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_content_id');
            
            $table->json('user_answer')->nullable(); // Array of selected answer indices
            $table->boolean('is_correct')->default(false);
            $table->integer('score_earned')->default(0);
            
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            
            $table->foreign('attempt_id')->references('id')->on('user_exam_attempts')->onDelete('cascade');
            $table->foreign('question_content_id')->references('id')->on('question_contents')->onDelete('cascade');
            
            $table->index('attempt_id');
            $table->index('question_content_id');
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
