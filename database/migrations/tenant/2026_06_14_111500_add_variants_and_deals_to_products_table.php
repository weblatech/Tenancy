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
        Schema::table('products', function (Blueprint $table) {
            $table->json('variants')->nullable()->after('images');
            $table->boolean('is_bundle')->default(false)->after('variants');
            $table->string('bundle_title')->nullable()->after('is_bundle');
            $table->decimal('bundle_price', 10, 2)->nullable()->after('bundle_title');
            $table->text('bundle_details')->nullable()->after('bundle_price');
            $table->boolean('is_discount')->default(false)->after('bundle_details');
            $table->string('discount_badge')->nullable()->after('is_discount');
            $table->text('discount_terms')->nullable()->after('discount_badge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'variants',
                'is_bundle',
                'bundle_title',
                'bundle_price',
                'bundle_details',
                'is_discount',
                'discount_badge',
                'discount_terms'
            ]);
        });
    }
};
