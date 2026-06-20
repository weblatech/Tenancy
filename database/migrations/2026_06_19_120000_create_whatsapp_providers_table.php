<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name')->default('meta'); // meta, twilio, etc.
            $table->text('api_key');
            $table->string('phone_number_id');
            $table->string('business_account_id')->nullable();
            $table->string('verify_token');
            $table->text('webhook_secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // extra provider-specific settings
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_providers');
    }
};
