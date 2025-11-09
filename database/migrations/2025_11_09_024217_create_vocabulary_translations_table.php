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
        Schema::create('vocabulary_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->onDelete('cascade');
            $table->string('language_code', 10); // vi, zh, en, ja, ko, es, fr, etc.
            $table->text('meaning');
            $table->text('example_translation')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['vocabulary_id', 'language_code']);
            $table->unique(['vocabulary_id', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vocabulary_translations');
    }
};
