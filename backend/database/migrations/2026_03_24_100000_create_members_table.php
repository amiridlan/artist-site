<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_english');
            $table->string('name_native')->nullable();
            $table->string('nickname')->nullable();
            $table->string('photo')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('generation', ['1st', '2nd']);
            $table->string('birthdate')->nullable();
            $table->integer('age')->nullable();
            $table->integer('height')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('hometown')->nullable();
            $table->json('hobbies')->nullable();
            $table->text('bio')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['active', 'graduated', 'concluded'])->default('active');
            $table->string('color')->nullable();
            $table->json('social')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
