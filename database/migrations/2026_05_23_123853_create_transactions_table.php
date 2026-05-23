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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->string('transaction_id')->nullable()->comment('Gateway specific transaction ID');
            $table->string('payment_type')->comment('e.g., stripe, paypal, google_iap, apple_iap');
            $table->string('payment_method_id')->nullable()->comment('Specific payment method used (e.g. Stripe pm_xxx)');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('idempotency_key')->nullable()->unique()->comment('To prevent duplicate processing');
            $table->json('payment_details')->nullable()->comment('Dynamic field to store full gateway response/metadata');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
