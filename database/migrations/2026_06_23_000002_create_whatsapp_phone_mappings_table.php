<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.database.central_connection'))->create('whatsapp_phone_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number_id')->unique()->index();
            $table->string('tenant_id')->index();
            $table->string('verify_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.database.central_connection'))->dropIfExists('whatsapp_phone_mappings');
    }
};
