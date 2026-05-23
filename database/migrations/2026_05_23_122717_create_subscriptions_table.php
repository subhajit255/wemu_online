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
            $table->longText('description')->nullable();
            $table->longText('features')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('interval')->nullable()->comment('1: month, 2: year');
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
