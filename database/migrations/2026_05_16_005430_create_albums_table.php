<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('category_id')->unsigned()->nullable()->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('genre_id')->unsigned()->nullable()->references('id')->on('genres')->onDelete('cascade');
            $table->foreignId('language_id')->unsigned()->nullable()->references('id')->on('languages')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('1 = active, 0 = inactive');
            $table->tinyInteger('status')->default(0)->comment('0 = draft, 1 = published');
            $table->date('release_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
