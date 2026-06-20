<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_sections', function (Blueprint $table) {
            $table->string('type')->default('custom_code'); // سیکشن کی قسم (custom_code یا discount_banner)
            $table->json('settings')->nullable(); // بغیر کوڈ والے سیکشنز کا ڈیٹا محفوظ کرنے کے لیے
        });
    }

    public function down(): void
    {
        Schema::table('store_sections', function (Blueprint $table) {
            $table->dropColumn(['type', 'settings']);
        });
    }
};