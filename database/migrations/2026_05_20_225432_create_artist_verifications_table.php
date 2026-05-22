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
        Schema::create('artist_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('government_id_front')->nullable();
            $table->string('government_id_back')->nullable();
            $table->string('selfie_image')->nullable();
            $table->tinyInteger('verification_status')->default(0)->comment('0-pending,1-approved,2-rejected');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_verifications');
    }
};
