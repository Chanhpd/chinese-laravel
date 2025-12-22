<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('time'); // Total time in minutes
            $table->enum('level', ['HSK1', 'HSK2', 'HSK3', 'HSK4', 'HSK5', 'HSK6'])->default('HSK1');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('level');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
