<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('whatsapp_crm_active')->default(false)->after('whatsapp_business_url');
            $table->text('whatsapp_api_key')->nullable()->after('whatsapp_crm_active');
            $table->string('whatsapp_phone_number_id')->nullable()->after('whatsapp_api_key');
            $table->string('whatsapp_verify_token')->nullable()->after('whatsapp_phone_number_id');
            $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_verify_token');
            $table->text('whatsapp_msg_order_pending')->nullable()->after('whatsapp_webhook_url');
            $table->text('whatsapp_msg_order_confirmed')->nullable()->after('whatsapp_msg_order_pending');
            $table->text('whatsapp_msg_order_processing')->nullable()->after('whatsapp_msg_order_confirmed');
            $table->text('whatsapp_msg_order_completed')->nullable()->after('whatsapp_msg_order_processing');
            $table->text('whatsapp_msg_order_cancelled')->nullable()->after('whatsapp_msg_order_completed');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_crm_active',
                'whatsapp_api_key',
                'whatsapp_phone_number_id',
                'whatsapp_verify_token',
                'whatsapp_webhook_url',
                'whatsapp_msg_order_pending',
                'whatsapp_msg_order_confirmed',
                'whatsapp_msg_order_processing',
                'whatsapp_msg_order_completed',
                'whatsapp_msg_order_cancelled',
            ]);
        });
    }
};
