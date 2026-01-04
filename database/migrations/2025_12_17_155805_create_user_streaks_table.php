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
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('streak_count')->default(0);
            $table->date('last_check_in_date')->nullable();
            $table->json('weekly_check_ins')->nullable(); // Array of check-in dates in current week
            $table->integer('total_check_in_days')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->timestamps();

            // Indexes
            $table->unique('user_id');
            $table->index('last_check_in_date');
            $table->index('streak_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_streaks');
    }
};
