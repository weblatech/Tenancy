<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. لامحدود سیکشنز (Unlimited Sections) کے لیے بالکل نیا ٹیبل
        Schema::create('store_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // سیکشن کا نام (جیسے: About Our Herbs)
            $table->longText('content'); // سیکشن کا کسٹم HTML کوڈ
            $table->integer('sort_order')->default(0); // اوپر نیچے کرنے کے لیے ترتیب
            $table->boolean('is_active')->default(true); // سیکشن کو چھپانے/دکھانے کا بٹن
            $table->timestamps();
        });

        // 2. پرانے سیٹنگز ٹیبل میں پروفیشنل فوٹر (Footer) کا اضافہ
        Schema::table('store_settings', function (Blueprint $table) {
            $table->text('footer_about')->nullable(); // سٹور کے بارے میں معلومات
            $table->string('footer_email')->nullable();
            $table->string('footer_phone')->nullable();
            $table->string('footer_facebook')->nullable();
            $table->string('footer_whatsapp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_sections');
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_about', 'footer_email', 'footer_phone', 'footer_facebook', 'footer_whatsapp']);
        });
    }
};