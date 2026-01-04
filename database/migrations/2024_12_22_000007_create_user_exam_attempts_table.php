<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exam_id');
            
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            
            $table->integer('total_score')->default(0);
            $table->integer('max_score')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->integer('time_spent')->default(0); // in seconds
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            
            $table->index('user_id');
            $table->index('exam_id');
            $table->index('status');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_exam_attempts');
    }
};
