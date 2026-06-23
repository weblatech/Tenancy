<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Make order_id nullable for chat messages and general messages
            $table->foreignId('order_id')->nullable()->change();

            // Add indexes for faster lookups by the webhook processor
            $table->index('provider_message_id');
            $table->index(['tenant_id', 'direction']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_logs', function ($table) {
            $table->dropIndex(['provider_message_id']);
            $table->dropIndex(['tenant_id', 'direction']);
            $table->dropIndex(['tenant_id', 'created_at']);
        });
    }
};
