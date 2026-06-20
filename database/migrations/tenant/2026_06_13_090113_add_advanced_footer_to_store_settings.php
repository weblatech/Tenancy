<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('footer_bg_color')->default('#4CAF50'); // سبز رنگ (Default)
            $table->string('footer_text_color')->default('#ffffff'); // سفید رنگ
            $table->text('footer_address')->nullable();
            $table->json('footer_quick_links')->nullable(); // کوئیک لنکس کے لیے
            $table->json('footer_policies_links')->nullable(); // پالیسی لنکس کے لیے
            $table->text('footer_newsletter_text')->nullable();
            $table->string('footer_copyright')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_bg_color', 'footer_text_color', 'footer_address', 'footer_quick_links', 'footer_policies_links', 'footer_newsletter_text', 'footer_copyright']);
        });
    }
};