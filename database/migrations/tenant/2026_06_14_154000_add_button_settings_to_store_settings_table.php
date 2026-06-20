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
            $table->string('btn_add_to_cart_text')->nullable()->default('ADD TO CART')->after('footer_copyright');
            $table->string('btn_add_to_cart_bg')->nullable()->default('#16a34a')->after('btn_add_to_cart_text');
            $table->string('btn_add_to_cart_text_color')->nullable()->default('#ffffff')->after('btn_add_to_cart_bg');
            $table->string('btn_buy_now_text')->nullable()->default('Order Now - Cash on Delivery')->after('btn_add_to_cart_text_color');
            $table->string('btn_buy_now_bg')->nullable()->default('#84cc16')->after('btn_buy_now_text');
            $table->string('btn_buy_now_text_color')->nullable()->default('#ffffff')->after('btn_buy_now_bg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'btn_add_to_cart_text',
                'btn_add_to_cart_bg',
                'btn_add_to_cart_text_color',
                'btn_buy_now_text',
                'btn_buy_now_bg',
                'btn_buy_now_text_color'
            ]);
        });
    }
};
