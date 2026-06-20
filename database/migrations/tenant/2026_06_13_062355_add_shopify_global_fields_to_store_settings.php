<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('facebook_pixel_id')->nullable(); // فیس بک پکسل آئی ڈی
            $table->boolean('enable_rtl')->default(false); // آر ٹی ایل ٹوگل
            $table->boolean('disable_inspect')->default(false); // رائٹ کلک بلاک ٹوگل
            $table->boolean('enable_sales_popup')->default(false); // سیلز پاپ اپ ٹوگل
            $table->json('sales_popup_data')->nullable(); // پاپ اپ کے اندر کا کسٹم ڈیٹا (نام اور شہر)
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['facebook_pixel_id', 'enable_rtl', 'disable_inspect', 'enable_sales_popup', 'sales_popup_data']);
        });
    }
};