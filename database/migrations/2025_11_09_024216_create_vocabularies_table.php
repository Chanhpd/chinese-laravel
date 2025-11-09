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
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->string('word');
            $table->string('phonetic')->nullable();
            $table->string('pinyin')->nullable();
            $table->string('simplified');
            $table->string('traditional')->nullable();
            $table->string('part_of_speech')->nullable(); // noun, verb, adjective, pronoun, etc.
            $table->text('meaning'); // Default English meaning
            $table->text('meaning_vi')->nullable();
            $table->text('meaning_zh')->nullable();
            $table->text('example_sentence')->nullable();
            $table->text('example_translation')->nullable();
            $table->string('example_highlight')->nullable();
            $table->text('definition')->nullable();
            $table->string('radical_info')->nullable();
            $table->integer('stroke_count')->nullable();
            $table->string('tone_pattern')->nullable();
            $table->json('related_words')->nullable(); // Array of related words
            $table->json('similar_chars')->nullable(); // Array of similar characters
            $table->string('pronunciation_audio')->nullable();
            $table->string('image_url')->nullable();
            $table->string('level')->nullable(); // HSK1, HSK2, HSK3, etc.
            $table->timestamps();
            
            // Indexes
            $table->index('topic_id');
            $table->index('level');
            $table->index('word');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vocabularies');
    }
};
