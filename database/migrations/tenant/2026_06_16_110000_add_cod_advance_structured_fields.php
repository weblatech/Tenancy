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
            $table->string('cod_advance_method')->default('easypaisa'); // bank, easypaisa, jazzcash
            $table->string('cod_advance_bank_name')->nullable();
            $table->string('cod_advance_account_title')->nullable();
            $table->string('cod_advance_account_number')->nullable();
            $table->string('cod_advance_easypaisa_title')->nullable();
            $table->string('cod_advance_easypaisa_number')->nullable();
            $table->string('cod_advance_jazzcash_title')->nullable();
            $table->string('cod_advance_jazzcash_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cod_advance_method',
                'cod_advance_bank_name',
                'cod_advance_account_title',
                'cod_advance_account_number',
                'cod_advance_easypaisa_title',
                'cod_advance_easypaisa_number',
                'cod_advance_jazzcash_title',
                'cod_advance_jazzcash_number'
            ]);
        });
    }
};
