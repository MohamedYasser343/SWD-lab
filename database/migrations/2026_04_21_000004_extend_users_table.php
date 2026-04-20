<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('reader')->index()->after('password');
            $table->string('username')->nullable()->unique()->after('name');
            $table->text('bio')->nullable()->after('username');
            $table->string('avatar')->nullable()->after('bio');
            $table->string('twitter')->nullable()->after('avatar');
            $table->string('website')->nullable()->after('twitter');
            $table->boolean('is_shadow_banned')->default(false)->after('website');
            $table->boolean('dark_mode')->default(false)->after('is_shadow_banned');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'username', 'bio', 'avatar', 'twitter',
                'website', 'is_shadow_banned', 'dark_mode',
            ]);
        });
    }
};
