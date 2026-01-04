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
        // Thêm 'staff' vào enum role
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin', 'staff') NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Xóa 'staff' khỏi enum role
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin') NOT NULL DEFAULT 'user'");
    }
};
