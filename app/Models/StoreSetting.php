<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'announcement_active', 'announcement_marquee', 'announcement_text', 'announcement_bg_color', 'announcement_text_color',
        'hero_title', 'hero_subtitle', 'hero_btn_text', 'hero_btn_link', 'hero_bg_color', 'hero_text_color', 'hero_layout_type', 'hero_image', 'hero_images', 'hero_custom_code',
        'hero_height', 'hero_align', 'hero_show_container', 'hero_overlay_opacity', 'hero_btn2_text', 'hero_btn2_link',
        'collection_title', 'collection_subtitle', 'collection_show_banner', 'collection_banner_bg', 'collection_banner_text_color',
        'header_logo', 'header_menu', 'footer_about', 'footer_email', 'footer_phone', 'footer_facebook', 'footer_whatsapp',
        'facebook_pixel_id', 'enable_rtl', 'disable_inspect', 'enable_sales_popup', 'sales_popup_data',
        // 👇 نئے فوٹر کے کالمز
        'footer_bg_color', 'footer_text_color', 'footer_address', 'footer_quick_links', 'footer_policies_links', 'footer_newsletter_text', 'footer_copyright', 'footer_bottom_bg_color', 'footer_bottom_text_color',
        // 👇 نئے بٹنز کے کالمز
        'btn_add_to_cart_text', 'btn_add_to_cart_bg', 'btn_add_to_cart_text_color',
        'btn_buy_now_text', 'btn_buy_now_bg', 'btn_buy_now_text_color',
        // 👇 نئے ڈیزائن کے کالمز
        'announcement_font_size', 'header_logo_height',
        'header_menu_bg', 'header_menu_text', 'header_menu_active_bg', 'header_menu_active_text',
        'btn_primary_bg', 'btn_primary_text', 'btn_secondary_bg', 'btn_secondary_text',
        // 👇 نئے پیمنٹ اور شپنگ کے کالمز
        'shipping_mode', 'shipping_flat_fee', 'shipping_threshold',
        'payment_cod_active', 'payment_bank_active', 'payment_bank_name', 'payment_bank_title', 'payment_bank_number',
        'payment_easypaisa_active', 'payment_easypaisa_title', 'payment_easypaisa_number',
        'payment_jazzcash_active', 'payment_jazzcash_title', 'payment_jazzcash_number',
        // 👇 COD Advance Payment Settings
        'cod_require_advance', 'cod_advance_type', 'cod_advance_value', 'cod_advance_instructions',
        'cod_advance_method',
        'cod_advance_bank_name', 'cod_advance_account_title', 'cod_advance_account_number',
        'cod_advance_easypaisa_title', 'cod_advance_easypaisa_number',
        'cod_advance_jazzcash_title', 'cod_advance_jazzcash_number',
        // Social Media & Tracking Integration
        'meta_pixel_active', 'meta_pixel_id', 'meta_pixel_events',
        'google_ads_active', 'google_ads_conversion_id', 'google_ads_conversion_label', 'google_analytics_id', 'google_tag_manager_id',
        'tiktok_pixel_active', 'tiktok_pixel_id',
        'snapchat_pixel_active', 'snapchat_pixel_id',
        'pinterest_tag_active', 'pinterest_tag_id',
        'twitter_pixel_active', 'twitter_pixel_id',
        'custom_tracking_active', 'custom_tracking_head', 'custom_tracking_body',
        'facebook_page_url', 'instagram_url', 'tiktok_url', 'youtube_url', 'twitter_url',
        'snapchat_url', 'pinterest_url', 'telegram_url', 'whatsapp_business_url',
        // WhatsApp CRM Settings
        'whatsapp_crm_active', 'whatsapp_api_key', 'whatsapp_phone_number_id',
        'whatsapp_verify_token', 'whatsapp_webhook_url',
        'whatsapp_msg_order_pending', 'whatsapp_msg_order_confirmed',
        'whatsapp_msg_order_processing', 'whatsapp_msg_order_completed',
        'whatsapp_msg_order_cancelled',
        'whatsapp_web_status',
    ];

    protected $casts = [
        'header_menu' => 'array',
        'sales_popup_data' => 'array',
        'footer_quick_links' => 'array', // 👈 نیا
        'footer_policies_links' => 'array', // 👈 نیا
        'hero_images' => 'array', // 👈 نیا
        'cod_require_advance' => 'boolean',
    ];
}