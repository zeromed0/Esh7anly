<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->decimal('balance', 10, 2)->default(0)->after('avatar');
            $table->boolean('is_verified')->default(false)->after('balance');
            $table->boolean('is_banned')->default(false)->after('is_verified');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'balance', 'is_verified', 'is_banned']);
        });
    }
};