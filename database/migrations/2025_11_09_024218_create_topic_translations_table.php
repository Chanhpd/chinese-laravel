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
        Schema::create('topic_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->string('language_code', 10); // de, en, es, fr, it, ja, ko, ru, vi, zh
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['topic_id', 'language_code']);
            $table->unique(['topic_id', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_translations');
    }
};
