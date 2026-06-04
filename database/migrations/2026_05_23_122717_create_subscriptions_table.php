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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->tinyInteger('available_for')->nullable()->comment('1: user, 2: artist');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('tagline')->nullable();
            $table->longText('description')->nullable();
            $table->longText('features')->nullable();
            
            // Billing & Payment Gateway
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('interval')->nullable()->comment('month, year, week');
            $table->integer('interval_count')->default(1);
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            
            // Spotify-specific features
            $table->integer('max_users')->default(1)->comment('1 for Individual, 2 for Duo, 6 for Family');
            $table->integer('trial_days')->default(0)->comment('e.g., 30 for 1 month free');
            $table->boolean('requires_verification')->default(false)->comment('True for Student plans');
            
            // Display
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0: in-active,  1: active');
            $table->tinyInteger('is_default')->default(0)->comment('0: not-default, 1: default');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
