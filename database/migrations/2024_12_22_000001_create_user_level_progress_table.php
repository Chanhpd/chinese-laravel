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
        Schema::create('user_level_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('level_id'); // Match with level table's int(11) - not unsigned
            
            $table->integer('completed_words')->default(0);
            $table->integer('total_words')->default(0);
            $table->integer('completed_radicals')->default(0);
            $table->integer('total_radicals')->default(0);
            
            $table->enum('mastery_level', ['beginner', 'intermediate', 'advanced', 'mastered'])->default('beginner');
            $table->timestamp('last_studied_at')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Note: level table uses int(11) without unsigned, so foreign key constraint may not work
            // We'll add index instead and handle referential integrity in application
            
            // Unique constraint: one progress record per user per level
            $table->unique(['user_id', 'level_id']);
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('level_id');
            $table->index('mastery_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_level_progress');
    }
};
