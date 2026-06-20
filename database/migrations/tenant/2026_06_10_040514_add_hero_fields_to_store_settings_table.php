<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('hero_title')->default('Welcome to our Store!');
            $table->string('hero_subtitle')->default('Discover our amazing collection.');
            $table->string('hero_bg_color')->default('#eff6ff'); // ہلکا نیلا
            $table->string('hero_text_color')->default('#1e3a8a'); // گہرا نیلا
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_title', 'hero_subtitle', 'hero_bg_color', 'hero_text_color']);
        });
    }
};