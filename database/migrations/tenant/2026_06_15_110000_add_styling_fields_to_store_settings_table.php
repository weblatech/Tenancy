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
            $table->string('announcement_font_size')->nullable()->default('14px')->after('announcement_text_color');
            $table->integer('header_logo_height')->nullable()->default(56)->after('header_logo');
            $table->string('header_menu_bg')->nullable()->default('#ffffff')->after('header_menu');
            $table->string('header_menu_text')->nullable()->default('#1f2937')->after('header_menu_bg');
            $table->string('header_menu_active_bg')->nullable()->default('#f3f4f6')->after('header_menu_text');
            $table->string('header_menu_active_text')->nullable()->default('#16a34a')->after('header_menu_active_bg');
            $table->string('btn_primary_bg')->nullable()->default('#16a34a')->after('btn_buy_now_text_color');
            $table->string('btn_primary_text')->nullable()->default('#ffffff')->after('btn_primary_bg');
            $table->string('btn_secondary_bg')->nullable()->default('#1f2937')->after('btn_primary_text');
            $table->string('btn_secondary_text')->nullable()->default('#ffffff')->after('btn_secondary_bg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'announcement_font_size',
                'header_logo_height',
                'header_menu_bg',
                'header_menu_text',
                'header_menu_active_bg',
                'header_menu_active_text',
                'btn_primary_bg',
                'btn_primary_text',
                'btn_secondary_bg',
                'btn_secondary_text',
            ]);
        });
    }
};
