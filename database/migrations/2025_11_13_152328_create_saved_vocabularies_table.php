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
        Schema::create('saved_vocabularies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vocabulary_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->integer('review_count')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps();
            
            // Unique constraint: a user can only save a vocabulary once
            $table->unique(['user_id', 'vocabulary_id']);
            
            // Indexes for faster queries
            $table->index('user_id');
            $table->index('vocabulary_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saved_vocabularies');
    }
};
