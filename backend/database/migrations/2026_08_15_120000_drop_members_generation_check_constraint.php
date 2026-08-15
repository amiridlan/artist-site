<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The original members table used enum('generation', ['1st', '2nd']), which on
 * Postgres is a varchar column plus a CHECK constraint — not a real enum type.
 * Staff now need to add new generation values from the admin UI, so this drops
 * that constraint on already-migrated databases (fresh installs no longer add
 * it at all, since the create_members_table migration was updated to a plain
 * string column).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE members DROP CONSTRAINT IF EXISTS members_generation_check');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE members ADD CONSTRAINT members_generation_check CHECK (generation IN ('1st', '2nd'))");
        }
    }
};
