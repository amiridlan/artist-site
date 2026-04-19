<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('social_media_platforms')->cascadeOnDelete();
            $table->string('content_id', 100);
            $table->string('type', 20);          // video, post, reel, tweet
            $table->string('title')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamp('published_at');
            $table->json('metrics');             // views, likes, comments, shares, reach, impressions, watch_time
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['platform_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_content_items');
    }
};
