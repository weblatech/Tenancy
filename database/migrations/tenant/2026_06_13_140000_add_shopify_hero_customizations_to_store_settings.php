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
            $table->string('hero_height')->default('medium')->after('hero_layout_type');
            $table->string('hero_align')->default('center')->after('hero_height');
            $table->boolean('hero_show_container')->default(false)->after('hero_align');
            $table->integer('hero_overlay_opacity')->default(50)->after('hero_show_container');
            $table->string('hero_btn2_text')->nullable()->after('hero_btn_link');
            $table->string('hero_btn2_link')->nullable()->after('hero_btn2_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_height',
                'hero_align',
                'hero_show_container',
                'hero_overlay_opacity',
                'hero_btn2_text',
                'hero_btn2_link',
            ]);
        });
    }
};
