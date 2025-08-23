<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['administrator', 'user'])->default('user')->after('password');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('address');
            $table->enum('theme_preference', ['light', 'dark', 'system'])->default('system')->after('avatar');
            $table->boolean('is_active')->default(true)->after('theme_preference');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            
            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'avatar', 'theme_preference', 'is_active', 'last_login_at']);
        });
    }
};