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
        Schema::create('landing_page_contents', function (Blueprint $table) {
            $table->id();
            $table->text('hero_title_one')->nullable();
            $table->text('hero_title_two')->nullable();
            $table->text('hero_content')->nullable();
            $table->text('app_url')->nullable();

            $table->text('feature_title')->nullable();
            $table->text('feature_description')->nullable();

            $table->text('feature_sub_title_one')->nullable();
            $table->text('feature_sub_desc_one')->nullable();
            $table->text('feature_sub_title_two')->nullable();
            $table->text('feature_sub_desc_two')->nullable();
            $table->text('feature_sub_title_three')->nullable();
            $table->text('feature_sub_desc_three')->nullable();
            $table->text('feature_sub_title_four')->nullable();
            $table->text('feature_sub_desc_four')->nullable();

            $table->text('hiw_title_one')->nullable(); //how it works title
            $table->text('hiw_desc_one')->nullable();
            $table->text('hiw_title_two')->nullable();
            $table->text('hiw_desc_two')->nullable();
            $table->text('hiw_title_three')->nullable();
            $table->text('hiw_desc_three')->nullable();

            $table->text('goal_title')->nullable();
            $table->text('goal_content')->nullable();

            $table->json('faqs')->nullable();

            $table->json('testimonials')->nullable();

            $table->text('testimonial_title_one')->nullable();
            $table->text('testimonial_title_two')->nullable();
            $table->text('testimonial_title_content')->nullable();
            $table->text('testimonial_ios_app_link')->nullable();
            $table->text('testimonial_android_app_link')->nullable();

            $table->text('footer_hours_desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_contents');
    }
};
