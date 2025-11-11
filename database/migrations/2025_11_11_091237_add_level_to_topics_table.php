<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('level', 10)->default('HSK1')->after('name_zh');
        });

        // Update existing topics based on sort_order
        // HSK1: sort_order 1-32
        DB::table('topics')
            ->whereBetween('sort_order', [1, 32])
            ->update(['level' => 'HSK1']);

        // HSK2: sort_order 33-62
        DB::table('topics')
            ->whereBetween('sort_order', [33, 62])
            ->update(['level' => 'HSK2']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
