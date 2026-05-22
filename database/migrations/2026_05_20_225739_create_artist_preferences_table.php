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
        Schema::create('artist_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            // Multi genre support
            $table->json('favorite_genres')->nullable();
            $table->tinyInteger('release_frequency')->nullable()->comment('1-weekly, 2-monthly, 3-occasionally');
            $table->enum('artist_type', ['INDEPENDENT','SIGNED'])->default('INDEPENDENT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_preferences');
    }
};
