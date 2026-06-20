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
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('announcement_marquee')->default(false)->after('announcement_active');
            $table->string('hero_btn_text')->nullable()->after('hero_subtitle');
            $table->string('hero_btn_link')->nullable()->after('hero_btn_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['announcement_marquee', 'hero_btn_text', 'hero_btn_link']);
        });
    }
};
