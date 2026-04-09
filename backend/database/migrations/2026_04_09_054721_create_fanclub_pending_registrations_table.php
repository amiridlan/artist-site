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
        Schema::create('fanclub_pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('password');
            $table->enum('tier', ['basic', 'gold']);
            $table->unsignedInteger('amount_cents');
            $table->string('bill_code')->nullable()->unique();
            $table->string('reference_no')->nullable();
            $table->timestamp('processed_at')->nullable(); // set when FanclubMember is created
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fanclub_pending_registrations');
    }
};
