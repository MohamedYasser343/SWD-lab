<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('status', 20)->default('published')->index()->after('slug');
            $table->timestamp('published_at')->nullable()->index()->after('status');
            $table->string('featured_image')->nullable()->after('body');
            $table->string('meta_title')->nullable()->after('featured_image');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
            $table->unsignedSmallInteger('reading_minutes')->nullable()->after('og_image');
            $table->unsignedInteger('views_count')->default(0)->after('reading_minutes');
            $table->unsignedInteger('likes_count')->default(0)->after('views_count');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'published_at', 'featured_image',
                'meta_title', 'meta_description', 'og_image',
                'reading_minutes', 'views_count', 'likes_count',
            ]);
        });
    }
};
