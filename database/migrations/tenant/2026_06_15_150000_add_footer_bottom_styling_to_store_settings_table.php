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
            $table->string('footer_bottom_bg_color')->nullable()->default('#1B5E20')->after('footer_copyright');
            $table->string('footer_bottom_text_color')->nullable()->default('#ffffff')->after('footer_bottom_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_bottom_bg_color',
                'footer_bottom_text_color',
            ]);
        });
    }
};
