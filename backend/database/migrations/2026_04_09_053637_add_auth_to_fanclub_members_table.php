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
        Schema::table('fanclub_members', function (Blueprint $table) {
            // nullable first so existing rows don't violate NOT NULL
            $table->string('password')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('password');
            $table->rememberToken()->after('email_verified_at');
        });

        // Backfill existing rows with a default hashed password ('password')
        \Illuminate\Support\Facades\DB::table('fanclub_members')
            ->whereNull('password')
            ->update(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        // Now enforce NOT NULL
        Schema::table('fanclub_members', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('fanclub_members', function (Blueprint $table) {
            $table->dropColumn(['password', 'email_verified_at', 'remember_token']);
        });
    }
};
