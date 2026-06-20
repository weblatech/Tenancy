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
            $table->boolean('cod_require_advance')->default(false);
            $table->string('cod_advance_type')->default('flat'); // flat, percentage
            $table->decimal('cod_advance_value', 10, 2)->default(0.00);
            $table->text('cod_advance_instructions')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('cod_advance_required', 10, 2)->default(0.00);
            $table->boolean('cod_advance_paid')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cod_require_advance',
                'cod_advance_type',
                'cod_advance_value',
                'cod_advance_instructions'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cod_advance_required',
                'cod_advance_paid'
            ]);
        });
    }
};
