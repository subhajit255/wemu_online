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
        Schema::create('play_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable()->comment('thumbnail or cober image');
            $table->boolean('is_public')->default(0)->comment('0=private, 1=public');
            $table->unsignedBigInteger('songs_count')->default(0);
            $table->unsignedBigInteger('followers_count')->default(0);
            $table->unsignedBigInteger('play_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_lists');
    }
};
