<?php
// Fix migration: replaces 2026_06_19_120001 - drops bad foreign keys and recreates tables
// Run this after deleting old migration record from migrations table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safely drop if exists (SQLite fallback)
        try { Schema::dropIfExists('whatsapp_messages'); } catch (\Exception $e) {}
        try { Schema::dropIfExists('whatsapp_conversations'); } catch (\Exception $e) {}

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('status')->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->timestamps();
            $table->index('tenant_id');
            $table->index('customer_phone');
            $table->index('status');
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('tenant_id');
            $table->string('direction');
            $table->string('message_type')->default('text');
            $table->text('message_body');
            $table->string('from_phone');
            $table->string('to_phone');
            $table->string('status')->default('sent');
            $table->string('provider_message_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->boolean('is_auto')->default(false);
            $table->timestamps();
            $table->index('conversation_id');
            $table->index('tenant_id');
            $table->index('direction');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
