<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->enum('type', ['music-video', 'performance', 'dance-practice', 'behind-the-scenes']);
            $table->string('youtube_id')->nullable();
            $table->string('thumbnail')->nullable();
            $table->date('date');
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
