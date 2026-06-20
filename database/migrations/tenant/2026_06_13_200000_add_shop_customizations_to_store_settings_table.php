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
            $table->string('collection_title')->nullable()->after('hero_overlay_opacity');
            $table->text('collection_subtitle')->nullable()->after('collection_title');
            $table->boolean('collection_show_banner')->default(true)->after('collection_subtitle');
            $table->string('collection_banner_bg')->default('#eff6ff')->after('collection_show_banner');
            $table->string('collection_banner_text_color')->default('#1e3a8a')->after('collection_banner_bg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'collection_title',
                'collection_subtitle',
                'collection_show_banner',
                'collection_banner_bg',
                'collection_banner_text_color',
            ]);
        });
    }
};
