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
            $table->string('bundle_header_title')->nullable()->after('bundle_details');
            $table->string('bundle_header_badge')->nullable()->after('bundle_header_title');
            $table->json('bundle_options')->nullable()->after('bundle_header_badge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'bundle_header_title',
                'bundle_header_badge',
                'bundle_options'
            ]);
        });
    }
};
