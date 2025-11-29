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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_english');
            $table->string('title_chinese')->nullable();
            $table->text('audio_url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('tags', 500)->nullable();
            $table->string('hsk_level', 10)->nullable()->index();
            $table->text('story_url')->nullable();
            $table->text('chinese_text')->nullable();
            $table->text('pinyin_text')->nullable();
            $table->mediumText('content_html')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
