<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_topic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->integer('completed_words')->default(0);
            $table->integer('total_words')->default(0);
            $table->enum('mastery_level', ['beginner', 'intermediate', 'advanced', 'mastered'])->default('beginner');
            $table->timestamp('last_studied_at')->nullable();
            $table->timestamps();
            
            // Unique constraint: one progress record per user per topic
            $table->unique(['user_id', 'topic_id']);
            
            // Indexes for faster queries
            $table->index('user_id');
            $table->index('topic_id');
            $table->index('mastery_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_topic_progress');
    }
};
