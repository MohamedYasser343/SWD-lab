<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 80);
            $table->string('day_key', 8);
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('viewed_at');
            $table->unique(['post_id', 'session_id', 'day_key'], 'post_views_dedup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_views');
    }
};
