<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('social_media_platforms')->cascadeOnDelete();
            $table->unsignedBigInteger('followers')->default(0);
            $table->unsignedBigInteger('views')->nullable();
            $table->unsignedInteger('posts')->nullable();
            $table->unsignedBigInteger('likes')->nullable();
            $table->unsignedBigInteger('comments')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['platform_id', 'fetched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_snapshots');
    }
};
