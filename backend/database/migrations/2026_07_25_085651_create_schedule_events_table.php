<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'artist_performance',
                'artist_appearance',
                'content_filming',
                'practice_day',
                'day_off',
                'staff_event',
                'social_media_post'
            ]);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('venue')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('kanban_card_id')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('conflict_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_datetime', 'end_datetime']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_events');
    }
};
