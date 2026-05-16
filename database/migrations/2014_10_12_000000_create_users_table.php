<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('username')->nullable()->unique()->comment('Users Username');
            $table->tinyInteger('user_type')->nullable()->comment('1-Super Admin,2:All Admin,3:Customer');
            $table->string('email')->nullable();
            $table->rememberToken();
            $table->string('password')->nullable();
            $table->string('pin', 10)->nullable();
            $table->bigInteger('phone_code')->nullable()->default(61);
            $table->bigInteger('mobile_number')->nullable();
            $table->string('verification_code')->nullable()->comment('OTP used for verifying the phone number');
            $table->string('registration_ip', 100)->nullable();
            $table->tinyInteger('is_verified')->default(true)->comment('0:Not Verified,1:Verified')->nullable();
            $table->tinyInteger('is_active')->default(true)->comment('0:Inactive,1:Active')->nullable();
            $table->tinyInteger('is_approve')->default(true)->comment('0:Unapproved,1:Approved')->nullable();
            $table->tinyInteger('is_blocked')->default(false)->comment('0:Unblocked,1:Blocked')->nullable();
            $table->tinyInteger('email_verified')->default(false)->comment('0:Unverified,1:Verified')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type')->default(1)->comment('1:Android,2:IOS')->nullable();
            $table->string('device_id')->nullable();
            $table->text('stripe_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['email', 'deleted_at'], 'users_email_deleted_at_unique');
            $table->unique(['mobile_number', 'deleted_at'], 'users_mobile_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
