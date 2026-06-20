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
            $table->string('shipping_mode')->default('conditional'); // free, flat, conditional
            $table->integer('shipping_flat_fee')->default(250);
            $table->integer('shipping_threshold')->default(2000);
            
            $table->boolean('payment_cod_active')->default(true);
            
            $table->boolean('payment_bank_active')->default(false);
            $table->string('payment_bank_name')->nullable();
            $table->string('payment_bank_title')->nullable();
            $table->string('payment_bank_number')->nullable();
            
            $table->boolean('payment_easypaisa_active')->default(false);
            $table->string('payment_easypaisa_title')->nullable();
            $table->string('payment_easypaisa_number')->nullable();
            
            $table->boolean('payment_jazzcash_active')->default(false);
            $table->string('payment_jazzcash_title')->nullable();
            $table->string('payment_jazzcash_number')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('cod'); // cod, bank, easypaisa, jazzcash
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_mode',
                'shipping_flat_fee',
                'shipping_threshold',
                'payment_cod_active',
                'payment_bank_active',
                'payment_bank_name',
                'payment_bank_title',
                'payment_bank_number',
                'payment_easypaisa_active',
                'payment_easypaisa_title',
                'payment_easypaisa_number',
                'payment_jazzcash_active',
                'payment_jazzcash_title',
                'payment_jazzcash_number',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
