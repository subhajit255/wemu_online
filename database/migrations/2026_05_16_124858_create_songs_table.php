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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('genre_id')->unsigned()->nullable()->references('id')->on('genres')->onDelete('cascade');
            $table->foreignId('language_id')->unsigned()->nullable()->references('id')->on('languages')->onDelete('cascade');
            $table->foreignId('album_id')->unsigned()->nullable()->references('id')->on('albums')->onDelete('cascade');
            $table->unsignedInteger('track_number')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('artist_name')->nullable();
            $table->string('featured_artists')->nullable();
            $table->longText('description')->nullable();
            $table->longText('lyrics')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->boolean('is_explicit')->default(0);
            $table->unsignedBigInteger('play_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->unsignedBigInteger('download_count')->default(0);
            $table->string('cover_image')->nullable();
            $table->string('audio_file')->nullable();
            $table->string('background')->nullable()->comment('image/video');
            $table->tinyInteger('status')->default(0)->comment('0=draft, 1=published');
            $table->timestamp('published_at')->nullable()->comment('date time of song published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
