<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "110001", "110002", "120001"
            $table->string('name'); // e.g., "Listening - Image matching"
            $table->string('part_type'); // "listening" or "reading"
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('part_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
