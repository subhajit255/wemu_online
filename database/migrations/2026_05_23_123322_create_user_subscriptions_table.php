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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('subscription_id')->unsigned()->nullable()->references('id')->on('subscriptions')->onDelete('cascade');
            
            // Payment Gateway specific
            $table->string('stripe_id')->unique()->nullable();
            $table->string('stripe_status')->nullable();
            
            // Billing periods
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            // Existing fields for reference/history
            $table->date('started_on')->nullable();
            $table->date('ended_at')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: in-active,  1: active, 2: expired, 3: cancelled');
            $table->string('transaction_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
