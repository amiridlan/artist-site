<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_geo_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('social_media_platforms')->cascadeOnDelete();
            $table->string('country_code', 5);
            $table->string('country_name', 100);
            $table->unsignedBigInteger('value');
            $table->decimal('percentage', 5, 2);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['platform_id', 'fetched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_geo_stats');
    }
};
