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
        Schema::create('artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('display_name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('bio')->nullable();
            $table->foreignId('primary_genre_id')->unsigned()->nullable()->references('id')->on('genres')->onDelete('cascade');
            $table->foreignId('sub_genre_id')->unsigned()->nullable()->references('id')->on('genres')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->integer('years_of_active')->default(0);
            $table->string('website')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('cover_banner')->nullable();
            $table->enum('artist_type', ['INDEPENDENT','SIGNED'])->default('INDEPENDENT');
            $table->tinyInteger('release_frequency')->nullable()->comment('1-weekly,2-monthly,3-occasionally');
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_profiles');
    }
};
