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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user')->after('password');
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('role');
            $table->timestamp('blocked_at')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('blocked_at');
            
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'blocked_at', 'last_login_at']);
        });
    }
};
