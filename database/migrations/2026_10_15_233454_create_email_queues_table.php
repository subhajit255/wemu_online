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
        Schema::create('email_queues', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->comment("TO MAIL");
            $table->boolean('status')->default(false)->comment("0 for pending, 1 for sent , 2 for failed");
            $table->text('error_message')->nullable()->comment("error message");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_queues');
    }
};
