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
        Schema::create('conflict_logs', function (Blueprint $table) {
            $table->id();
            $table->string('conflictable_type');
            $table->unsignedBigInteger('conflictable_id');
            $table->enum('conflict_type', [
                'artist_double_booking',
                'artist_day_off_conflict',
                'staff_availability',
                'resource_conflict'
            ]);
            $table->json('details');
            $table->enum('resolution', ['pending', 'overridden', 'resolved'])->default('pending');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['conflictable_type', 'conflictable_id'], 'conflict_logs_conflictable_idx');
            $table->index('conflict_type');
            $table->index('resolution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflict_logs');
    }
};
