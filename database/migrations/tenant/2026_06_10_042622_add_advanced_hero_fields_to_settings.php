<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('hero_layout_type')->default('color'); // یہ بتائے گا کہ کلر ہے، امیج ہے یا کوڈ
            $table->string('hero_image')->nullable(); // تصویر کا لنک
            $table->longText('hero_custom_code')->nullable(); // کسٹم کوڈ
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_layout_type', 'hero_image', 'hero_custom_code']);
        });
    }
};