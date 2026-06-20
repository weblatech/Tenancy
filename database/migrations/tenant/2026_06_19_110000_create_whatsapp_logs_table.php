<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tenant_id');
            $table->string('direction'); // outbound, inbound
            $table->string('message_type'); // order_pending, order_confirmed, order_processing, order_completed, order_cancelled, customer_confirm, customer_cancel
            $table->text('message_body');
            $table->string('to_phone');
            $table->string('from_phone')->nullable();
            $table->string('status')->default('sent'); // sent, delivered, read, failed
            $table->string('provider_message_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
