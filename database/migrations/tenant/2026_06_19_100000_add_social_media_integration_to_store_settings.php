<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('meta_pixel_active')->default(false);
            $table->string('meta_pixel_id')->nullable();
            $table->text('meta_pixel_events')->nullable();

            $table->boolean('google_ads_active')->default(false);
            $table->string('google_ads_conversion_id')->nullable();
            $table->string('google_ads_conversion_label')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->text('google_tag_manager_id')->nullable();

            $table->boolean('tiktok_pixel_active')->default(false);
            $table->string('tiktok_pixel_id')->nullable();

            $table->boolean('snapchat_pixel_active')->default(false);
            $table->string('snapchat_pixel_id')->nullable();

            $table->boolean('pinterest_tag_active')->default(false);
            $table->string('pinterest_tag_id')->nullable();

            $table->boolean('twitter_pixel_active')->default(false);
            $table->string('twitter_pixel_id')->nullable();

            $table->boolean('custom_tracking_active')->default(false);
            $table->longText('custom_tracking_head')->nullable();
            $table->longText('custom_tracking_body')->nullable();

            $table->string('facebook_page_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('snapchat_url')->nullable();
            $table->string('pinterest_url')->nullable();
            $table->string('telegram_url')->nullable();
            $table->string('whatsapp_business_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_active', 'meta_pixel_id', 'meta_pixel_events',
                'google_ads_active', 'google_ads_conversion_id', 'google_ads_conversion_label', 'google_analytics_id', 'google_tag_manager_id',
                'tiktok_pixel_active', 'tiktok_pixel_id',
                'snapchat_pixel_active', 'snapchat_pixel_id',
                'pinterest_tag_active', 'pinterest_tag_id',
                'twitter_pixel_active', 'twitter_pixel_id',
                'custom_tracking_active', 'custom_tracking_head', 'custom_tracking_body',
                'facebook_page_url', 'instagram_url', 'tiktok_url', 'youtube_url', 'twitter_url',
                'snapchat_url', 'pinterest_url', 'telegram_url', 'whatsapp_business_url',
            ]);
        });
    }
};
