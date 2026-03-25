<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->enum('type', ['concert', 'meet-greet', 'handshake', 'online', 'other']);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled']);
            $table->date('date');
            $table->date('end_date')->nullable();
            $table->string('venue')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('ticket_url')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
