<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            
            // 📢 Announcement Bar (اعلانیہ پٹی) کا کنٹرول
            $table->boolean('announcement_active')->default(true); // پٹی دکھانی ہے یا چھپانی ہے؟
            $table->string('announcement_text')->default('🎉 Welcome to our store! Huge discounts inside.'); // پٹی پر کیا لکھا ہو؟
            $table->string('announcement_bg_color')->default('#1e3a8a'); // پٹی کا بیک گراؤنڈ کلر
            $table->string('announcement_text_color')->default('#ffffff'); // ٹیکسٹ کا کلر
            
            // (مستقبل میں ہم Header اور Hero Section کے کالمز بھی یہیں شامل کریں گے)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};