<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // پروڈکٹ کے ساتھ لنک
            $table->string('customer_name');
            $table->integer('rating')->default(5); // 1 سے 5 تک سٹارز
            $table->text('comment');
            $table->boolean('show_on_homepage')->default(true); // ہوم پیج پر دکھانے کا ٹوگل
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};