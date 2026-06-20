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
            $table->string('hero_title')->nullable()->change();
            $table->string('hero_subtitle')->nullable()->change();
            $table->string('announcement_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('hero_title')->default('Welcome to our Store!')->change();
            $table->string('hero_subtitle')->default('Discover our amazing collection.')->change();
            $table->string('announcement_text')->default('🎉 Welcome to our store! Huge discounts inside.')->change();
        });
    }
};
